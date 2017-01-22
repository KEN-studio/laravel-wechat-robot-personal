<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Message as VbotMessage;
use Hanson\Vbot\Message\Entity\Image as VbotImage;
use Hanson\Vbot\Message\Entity\Text as VbotText;
use Hanson\Vbot\Message\Entity\Emoticon as VbotEmoticon;
use Hanson\Vbot\Message\Entity\Location as VbotLocation;
use Hanson\Vbot\Message\Entity\Video as VbotVideo;
use Hanson\Vbot\Message\Entity\Voice as VbotVoice;
use Hanson\Vbot\Message\Entity\Recall as VbotRecall;
use Hanson\Vbot\Message\Entity\RedPacket as VbotRedPacket;
use Hanson\Vbot\Message\Entity\Transfer as VbotTransfer;
use Hanson\Vbot\Message\Entity\Recommend as VbotRecommend;
use Hanson\Vbot\Message\Entity\Share as VbotShare;
use Hanson\Vbot\Message\Entity\Touch as VbotTouch;
use Hanson\Vbot\Message\Entity\RequestFriend as VbotRequestFriend;

class Robot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vbot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'vbot Command';

    /**
     * 可执行的任务
     *
     * @var array
     */
    protected $tasks = [
        'debug',
        'mass',
        'standby',
    ];

    protected $vbot;
    protected $vbotStoragePath;
    protected $vbotDebugMode;
    protected $process;
    protected $wechatUserId;
    protected $wechatNickname;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 调试模式
        $this->vbotDebugMode = $this->ask('debug mode', true);

        // 微信号
        $this->wechatUserId = $this->ask('your WechatId', config('vbot.default_wechat_id'));
        $this->wechatNickname = $this->ask('your WechatNickname', config('vbot.default_wechat_nickname'));

        // 存储路径
        $this->vbotStoragePath = storage_path('vbot/' . $this->wechatUserId);

        // 任务
        $task = $this->ask('which task', 'standby');

        // 不在预定义的任务列表？
        if (!in_array($task, $this->tasks)) {
            $this->error("\n[ Task does not exist ]: {$task}");
            return null;
        }

        // 执行确认
        if (!$this->confirm("[ Run task ]: {$task}")) {
            return null;
        }

        // VBOT 实例化
        $this->vbot = new Vbot([
            'tmp' => $this->vbotStoragePath,
            'debug' => $this->vbotDebugMode,
        ]);

        // 调取任务
        try {
            $this->$task();
        } catch (\Exception $e) {
            $this->error("[Error #.{$e->getCode()}] Line.{$e->getLine()} {$e->getMessage()}");
        }

        // 其它设备登录，网页版微信退出
        $this->vbot->server->setExitHandler(function () {
            $this->warn('其他设备登录');
        });

        // 异常退出
        $this->vbot->server->setExceptionHandler(function () {
            $this->warn('异常退出');
        });

        // VBOT 启动工作
        $this->vbot->server->run();
    }

    /**
     * 调试命令
     */
    public function debug()
    {
        if (!$this->confirm('群发确认')) {
            return null;
        }

        $this->vbot->server->setCustomerHandler(function () {

            contact()->each(function ($contact) {
                $this->warn("{$contact['UserName']} : {$contact['Alias']}: {$contact['NickName']} : {$contact['RemarkName']}");
            });

            exit();
        });
    }

    /**
     * 群发测试，随机时间间隔
     */
    public function mass()
    {
        $this->vbot->server->setCustomerHandler(function () {

            $text = "测试 ".Carbon::now()->format('Y-m-d H:i:s');

            contact()->each(function ($contact) use ($text) {
                $this->warn("微信名：{$contact['NickName']}\n备注名：{$contact['RemarkName']}");

                sleep(rand(5, 15));

                VbotText::Send($contact['UserName'], $text);
            });

            exit();
        });
    }

    /**
     * 一般守护
     */
    public function standby()
    {
        $this->vbot->server->setMessageHandler(function ($message) {

            // 被请求添加好友信息
            if ($message instanceof VbotRequestFriend) {
                // 通知至一个微信群
                $groupUsername = group()->getGroupsByNickname(config('vbot.default_wechat_groupname'), true)->first()['UserName'];

                $tip = "{$message->info['UserName']} {$message->info['NickName']} 请求添加好友 \"{$message->info['Content']}\"";

                $this->info($tip);

                if ($message->info['Content'] === '拜见主上') {
                    VbotText::Send($message->info['UserName'], "平身~\n赐座~~");
                    VbotText::Send($groupUsername, "{$tip}\n\n暗号正确");
                    $message->verifyUser($message::VIA);
                } else {
                    VbotText::Send($groupUsername, "{$tip}\n\n暗号错误");
                }
            }

            // 文字信息
            if ($message instanceof VbotText) {
                switch ($message->fromType) {

                    // 联系人自动回复
                    case 'Contact': {
                        $this->warn("\n[ {$message->from['NickName']} ]# {$message->content}");

                        $reply = $this->reply($message->content, $message->from['Alias']);

                        $this->info("\n[ REPLY ]$ {$reply}");

                        return $reply;
                        break;
                    }

                    // 群组
                    case 'Group': {

                        // @我
                        if ($message->isAt) {

                            $content = preg_replace('/^@' . $this->wechatNickname . '\s/', '', $message->content);

                            $this->warn("\n[ {$message->sender['NickName']} @{$this->wechatNickname} via {$message->from['NickName']} ]# {$content}");

                            $reply = "@{$message->sender['NickName']}\n\n" . $this->reply($content, md5($message->from['NickName']));

                            $this->info("\n[ REPLY ]$ {$reply}");

                            return $reply;

                        } else {

                            // 直呼提到我
                            if (preg_match('/^' . $this->wechatNickname . '*/', $message->content)) {

                                $content = preg_replace('/^' . $this->wechatNickname . '/', '', $message->content);

                                $this->warn("\n[ {$message->sender['NickName']} ^{$this->wechatNickname} via {$message->from['NickName']} ]# {$content}");

                                $reply = "@{$message->sender['NickName']}\n\n" . $this->reply($content, md5($message->from['NickName']));

                                $this->info("\n[ REPLY ]$ {$reply}");

                                return $reply;

                            // 没直接提到我
                            } else {

                                $this->warn("\n[ {$message->sender['NickName']} via {$message->from['NickName']} ]# {$message->content}");

                            }
                        }
                        break;
                    }
                }
            }

            // 图片信息 返回接收到的图片
            if ($message instanceof VbotImage) {
                $this->warn("\n[ {$message->from['NickName']} ]# 发送了一张图片");

                return $message;
            }

            // 视频信息 返回接收到的视频
            if ($message instanceof VbotVideo) {
                $this->warn("\n[ {$message->from['NickName']} ]# 发送了一个视频");

                return $message;
            }

            // 表情信息 返回接收到的表情
            if ($message instanceof VbotEmoticon) {
                $this->warn("\n[ {$message->from['NickName']} ]# 发送了一个表情");

                return $message;
            }

            // 语音消息
            if ($message instanceof VbotVoice) {
                $this->warn("\n[ {$message->from['NickName']} ]# 发送了一段语音");

                return '收到一条语音并下载在' . $message->getPath($message::$folder) . "/{$message->msg['MsgId']}.mp3";
            }

            // 撤回信息
            if ($message instanceof VbotRecall && $message->msg['FromUserName'] !== myself()->username) {
                if ($message->origin instanceof VbotImage) {
                    $this->warn("\n[ {$message->nickname} ]# 撤回了一张照片");

                    VbotText::Send($message->msg['FromUserName'], "{$message->nickname} 撤回了一张照片");
                    VbotImage::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
                } elseif ($message->origin instanceof VbotEmoticon) {
                    $this->warn("\n[ {$message->nickname} ]# 撤回了一个表情");

                    VbotText::Send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个表情");
                    VbotEmoticon::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
                } elseif ($message->origin instanceof VbotVideo) {
                    $this->warn("\n[ {$message->nickname} ]# 撤回了一个视频");

                    VbotText::Send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个视频");
                    VbotVideo::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
                } elseif ($message->origin instanceof VbotVoice) {
                    $this->warn("\n[ {$message->nickname} ]# 撤回了一条语音");

                    VbotText::Send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条语音");
                } else {
                    $this->warn("\n[ {$message->nickname} ]# 撤回了一条信息： {$message->origin->msg['Content']}");

                    VbotText::Send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条信息 \"{$message->origin->msg['Content']}\"");
                }
            }

            // 红包信息
            if ($message instanceof VbotRedPacket) {
                // do something to notify if you want ...
                $this->warn("\n[ {$message->from['NickName']} ]# 撤回了一条语音");

                return $message->content . ' 来自 ' . $message->from['NickName'];
            }

            // 转账信息
            if ($message instanceof VbotTransfer) {
                $this->warn("\n[ {$message->from['NickName']} ]# 转账金额 {$message->fee}");

                return $message->content . ' 转账金额 ' . $message->fee;
            }

            // 推荐名片信息
            if ($message instanceof VbotRecommend) {
                if ($message->isOfficial) {
                    $this->warn("\n[ {$message->from['NickName']} ]# 向你推荐了公众号 {$message->info['NickName']}");

                    return $message->from['NickName'] . ' 向你推荐了公众号 ' . $message->province . $message->city .
                        " {$message->info['NickName']} 公众号信息： {$message->description}";
                } else {
                    $this->warn("\n[ {$message->from['NickName']} ]# 向你推荐了名片 {$message->info['NickName']}");

                    return $message->from['NickName'] . ' 向你推荐了 ' . $message->province . $message->city .
                        " {$message->info['NickName']} 头像链接： {$message->bigAvatar}";
                }
            }

            // 分享信息
            if ($message instanceof VbotShare) {
                $this->warn("\n[ {$message->from['NickName']} ]# 分享标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}");

                $reply = "收到分享\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}";
                if ($message->app) {
                    $reply .= "\n来源APP：{$message->app}";
                }
                return $reply;
            }

            // 位置信息 返回位置文字
            if ($message instanceof VbotLocation) {
                $this->warn("\n[ {$message->from['NickName']} ]# 位置 {$message}");

                VbotText::Send('地图链接：' . $message->from['UserName'], $message->url);
                return '位置：' . $message;
            }

            // 手机点击聊天事件
            if ($message instanceof VbotTouch) {
                dump($message);

                VbotText::Send($message->msg['ToUserName'], "我点击了此聊天");
            }

            return false;
        });
    }

    /**
     * 使用图灵机器人回复
     *
     * @param $str
     * @param null $userId
     * @return string
     */
    public function reply($str, $userId = null)
    {
        $data = [];
        $data['key'] = config('turing.key');
        $data['info'] = $str;
        $userId ? ($data['userid'] = md5($userId)) : null;

        return http()->post('http://www.tuling123.com/openapi/api', $data, true)['text'];
    }
}

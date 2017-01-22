<?php

namespace App\Console\Commands;

use Cache;
use Carbon\Carbon;
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

class Bainian extends VbotBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bainian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拜年';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->tasks[] = ['name' => 'touchSendAndReply', 'description' => '点击对话自动发送预设的祝福、联系人回复消息自动发送预设的回应'];
        $this->tasks[] = ['name' => 'mass', 'description' => '群发（随机时间间隔，结束自动退出，期间不会进行消息响应）'];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->callHandle();
    }

    /**
     * 群发（随机时间间隔，结束自动退出，期间不会进行消息响应）
     */
    public function mass()
    {
        $text = "测试内容";

        $this->warn("\n群发内容：\n");
        $this->info($text);

        if (!$this->confirm("\n扫码登录后将，自动启动群发")) {
            return null;
        }

        $this->vbot->server->setCustomerHandler(function () use ($text) {
            do {
                contact()->each(function ($contact) use ($text) {
                    $this->warn("\n{$contact['UserName']}");
                    $this->info("[微信号] {$contact['Alias']}\n[ 昵称 ] {$contact['NickName']}\n[ 备注 ] {$contact['RemarkName']}");

                    sleep(rand(5, 15));
                    VbotText::Send($contact['UserName'], $text);
                });

                $done = true;
            } while ($done == false);

            // 群发完毕后守候继续进行
        });
    }


    /**
     * 点击对话自动发送预设的祝福、联系人消息自动回复预设的回应
     */
    public function touchSendAndReply()
    {
        // 预置顺序我的祝福（点触对话一次，发送一次）
        $wishes = [
            1 => "春节快乐，大吉大利！",
            2 => "恭喜发财，红包拿来！",
        ];

        // 预置顺序我的回复（收到消息一次，发送一次）
        $replies = [
            1 => "么么哒~~\n加油~~\n先为身边的朋友送去祝福~\n\n咱迟后再续…",
            2 => "[福][福][福]\n[鸡][鸡][鸡][鸡][鸡][鸡]",
        ];

        $this->vbot->server->setMessageHandler(function ($message) use ($wishes, $replies) {

            // 若遇异常只需报出异常信息而不中断进程
            try {

                // 手指点入聊天：自动发送祝福
                if ($message instanceof VbotTouch) {
                    $touchTimes = Cache::get("UserName_{$message->msg['ToUserName']}_touch", 0);

                    Cache::forever("UserName_{$message->msg['ToUserName']}_touch", ++$touchTimes);

                    if (count($wishes) > $touchTimes - 1) {
                        $this->info("\n点出至 [{$message->msg['ToUserName']}] 的第{$touchTimes}条出祝福");

                        VbotText::Send($message->msg['ToUserName'], $wishes[$touchTimes]);
                    }
                }

                // 联系人发来文字信息
                if ($message instanceof VbotText) {
                    switch ($message->fromType) {

                        // 联系人自动回复
                        case 'Contact': {
                            $replyTimes = Cache::get("UserName_{$message->from['UserName']}_reply", 0);

                            Cache::forever("UserName_{$message->from['UserName']}_reply", ++$replyTimes);

                            $this->warn("\n来自 [{$message->from['NickName']}] 的第{$replyTimes}个消息\n\n# {$message->content}");

                            if (count($replies) >= $replyTimes) {
                                $this->info("\n{$replies[$replyTimes]}");

                                sleep(1);
                                return "{$replies[$replyTimes]}";
                            }

                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("\n\n[Error #.{$e->getCode()}] Line.{$e->getLine()} {$e->getMessage()}\n");
            }

            return false;
        });
    }
}

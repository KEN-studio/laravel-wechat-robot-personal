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

class Debug extends VbotBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '提交项目 PR 请勿占用此命令';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->tasks[] = ['name' => 'debug', 'description' => '调试'];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->callHandle();
    }

    /**
     * 调试用
     */
    public function debug()
    {
        $this->vbot->server->setCustomerHandler(function () {

            // 若遇异常只需报出异常信息而不中断进程
            try {
                contact()->each(function ($contact) {
                    $this->warn("\n{$contact['UserName']}");
                    $this->info("[微信号] {$contact['Alias']}\n[ 昵称 ] {$contact['NickName']}\n[ 备注 ] {$contact['RemarkName']}");
                });

                exit();
            } catch (\Exception $e) {
                $this->error("\n\n[Error #.{$e->getCode()}] Line.{$e->getLine()} {$e->getMessage()}\n");
            }

            return false;
        });
    }
}

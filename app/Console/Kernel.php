<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Message as VBotMessage;
use Hanson\Vbot\Message\Entity\Image as VBotImage;
use Hanson\Vbot\Message\Entity\Text as VBotText;
use Hanson\Vbot\Message\Entity\Emoticon as VBotEmoticon;
use Hanson\Vbot\Message\Entity\Location as VBotLocation;
use Hanson\Vbot\Message\Entity\Video as VBotVideo;
use Hanson\Vbot\Message\Entity\Voice as VBotVoice;
use Hanson\Vbot\Message\Entity\Recall as VBotRecall;
use Hanson\Vbot\Message\Entity\RedPacket as VBotRedPacket;
use Hanson\Vbot\Message\Entity\Transfer as VBotTransfer;
use Hanson\Vbot\Message\Entity\Recommend as VBotRecommend;
use Hanson\Vbot\Message\Entity\Share as VBotShare;
use Hanson\Vbot\Message\Entity\Touch as VBotTouch;
use Hanson\Vbot\Message\Entity\RequestFriend as VBotRequestFriend;
use Hanson\Vbot\Support\Console as VBotConsole;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Vbot::class,
        Commands\Standby::class,
        Commands\Debug::class,

        Commands\Bainian::class,
        Commands\Test::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}

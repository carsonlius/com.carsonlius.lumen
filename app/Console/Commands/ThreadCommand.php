<?php


namespace App\Console\Commands;

use App\Model\MongoCrawlerTel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ThreadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thread:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "关于多线程的测试";

    protected $bar;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $list_tel_list = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->_handleDo();
        } catch (\Exception $e) {
            $this->output->error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile());
        } catch (\Error $e) {
            $this->output->error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile());
        }
    }

    private function _handleDo()
    {
        $this->output->success('中文 我的世界');

        $limit = 2858114;
        $option = [
            'projection' => [
                'tel' => 1,
                'apikey' => 1,
                '_id' => 0
            ],
//        "batchSize" => 100
//        'limit' => 1800000
        ];
        $cursor = \DB::connection('mongodb_self')->collection('crawler_tels')->raw(function ($collection) use ( $option) {
            return $collection->find([], $option);
        });

        $memory_start  = memory_get_usage();

        // ????????
//        $bar =$this->output->createProgressBar($limit);
        $start_time = microtime(true);
        $i = 0;
        foreach ($cursor as $item) {
//            MongoCrawlerTel::create([
//                'apikey' => $item->apikey,
//                'product_id' => $item->product_id,
//                'tel' => $item->tel,
//                'amount_date' => $item->amount_date,
//                'crawler' => $item->crawler,
//                'operator' => $item->operator,
//                'hash_key' => $item->hash_key,
//            ]);

            $i++;
            $this->list_tel_list['all'][$item->tel] = '';
            $this->list_tel_list['list_apikey'][$item->apikey][$item->tel] = '';
//            $bar->advance();
        }

//        $bar->finish();
        $end_time = microtime(true);
        $memory_end = memory_get_usage();
        $memory_now = $memory_end/(1024*1024);
        $memory_need = ($memory_end - $memory_start)/(1024*1024);
        $memory_get_peak_usage  = memory_get_peak_usage()/(1024*1024);

        $msg = '需要内存 ' . $memory_need . 'MB ,系统分配内存: ' . $memory_get_peak_usage . ' MB now memory: ' . $memory_now. ' MB 总共轮询 : ' . $i . ' 消耗时间 ' . ($end_time - $start_time) . 's, 唯一号码量 ' . count($this->list_tel_list['all'] ?? []);

        Log::debug('debug', [$msg]);
        $this->output->success('end'  . PHP_EOL . $msg . PHP_EOL);
    }

}

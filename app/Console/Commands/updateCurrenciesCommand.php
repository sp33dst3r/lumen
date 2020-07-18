<?php
/**
 *
 * PHP version >= 7.0
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */

namespace App\Console\Commands;


use App\Currency;

use Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client;



/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class UpdateCurrenciesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "update:currencies";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update all currencies";


    /**
     * Execute the console command.
     *
     * @return mixed
     */

    

    public function getEngName($currs, $num)
    {
        foreach($currs as $item){
            
            if((string)$item->NumCode == (string)$num) {
               
          
                return $item->Name;
            }
            
        }  
        return "None";
    }
    public function handle()
    {
        try {
            Currency::truncate();
            $currs = Currency::all();
          
            $day = date("d/m/Y", time());

            $client = new \GuzzleHttp\Client();
            $res = $client->get('http://www.cbr.ru/scripts/XML_daily.asp?date_req='.$day, ['']);
            $eng_res = $client->get('http://www.cbr.ru/scripts/XML_daily_eng.asp?date_req='.$day, ['']);
            $status = $res->getStatusCode(); // 200
            $eng_status = $eng_res->getStatusCode(); // 200
            
            if($status == 200 && $eng_status==200){
                
                
                $contents = $res->getBody()->getContents();
                $eng_contents = $eng_res->getBody()->getContents();
                $xml = simplexml_load_string($contents);
                $eng_xml = simplexml_load_string($eng_contents);
               
                foreach($xml as $currency){
                    $curr = new Currency();
                    $curr->name = $currency->Name;
                    $curr->curr_id = $currency->attributes()->ID[0];
                    $curr->alphabetic_code = $currency->CharCode;
                    $curr->name = $currency->Name;
                    $eng = $this->getEngName($eng_xml, $currency->NumCode);
                   
                        $curr->english_name = $eng;
                    
                    
                    $curr->rate = $currency->Value / $currency->Nominal;
                    $curr->digit_code = $currency->NumCode;
                    $curr->save(); 
                    
                }
                echo "updated";
                
            }
 
        } catch (Exception $e) {
            $this->error("An error occurred");
        }
    }
}
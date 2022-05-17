<?php
namespace App\Packages\QueryExtra;

/**
 * Run complex queries in Laravel
 *
 * @package  laravel-query-extra
 * @author   Md. Rakibul Islam <rakib1708@gmail.com>
 * @version  dev-master
 * @since    2021-03-21
 */

use App\Packages\QueryExtra\Options\BulkUpdate;
use App\Packages\QueryExtra\Traits\ProcessQuery;

class QueryExtra 
{
	use ProcessQuery;

	/**
	 * Update different conditional data in a single query.
     * 
     * @param  array $update
     * @return boolean
    */

	public function bulkup(array $update)
	{
		$this->makeQueryForUpdate($update)->run();
	}

	/**
	 * make and set query for bulk update
     * 
     * @param  array $update
     * @return $this
     * update array basic structure
       $update = [
    		[
    			'data' => [
    				'field_1' => 'field_1_v1lue_a',
    				'field_2' => 'field_2_v1lue_a'
    			],
    			'value' => 2
    		],
    		[
    			'data' => [
    				'field_2' => 'field_1_v1lue_b',
    				'field_2' => 'field_1_v1lue_b'
    			],
    			'value' => 3
    		],
    	];
     *
     */

	protected function makeQueryForUpdate(array $update)
	{
		$this->query = (new BulkUpdate)->make($this->table, $this->whereKey, $update);
		return $this;
	}

}
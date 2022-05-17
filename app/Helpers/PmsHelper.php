<?php
 function categoryList(){

     return \App\Models\PmsModels\Category::where('parent_id',null)->get();
}

function status(){
	return array(
		"active"=>'Active',
		"inactive"=>'Inactive',
		"cancel"=>'Cancel',
	);
}

function statusArray(){
	return array(
		0 => "Pending",
		1 => "Approved",
		2 => "Halt",
	);
}

function statusArrayForHead(){
	return array(
		0 => "Pending",
		1 => "Acknowledge",
		2 => "Halt",
	);
}

function stringStatusArray(){
	return array(
		'pending' => "Pending",
		'approved' => "Approved",
		'Halt' => "Halt",
	);
}

function deliveryStatus(){
	return array(
		'processing' => "Processing",
		'confirmed' => "Confirmed",
		'purchase' => "Purchase",
		'delivered' => "Delivered",
		'partial-delivered' => "Percial-Delivery",
		'cencel' => "Cencel",
	);
}

function maritalStatus(){
	return array(
		'Single',
		'Married',
		'Divorced',
	);
}

function bloodGroups(){
	return array(
		'N/A',
		'A+',
		'A-',
		'B+',
		'B-',
		'O+',
		'O-',
		'AB+',
		'AB-',
	);
}

function weekDays(){
	return array(
		"Monday",
		"Tuesday",
		"Wednesday",
		"Thursday",
		"Friday",
		"Saturday",
		"Sunday",
	);
}

function weekDaysIndex(){
	return array(
		"Monday" => 0,
		"Tuesday" => 1,
		"Wednesday" => 2,
		"Thursday" => 3,
		"Friday" => 4,
		"Saturday" => 5,
		"Sunday" => 6,
	);
}

function minutesDifference($from,$to)
{
	$start_date = new DateTime($from);
	$since_start = $start_date->diff(new DateTime($to));
	$minutes = $since_start->days * 24 * 60;
	$minutes += $since_start->h * 60;
	$minutes += $since_start->i;
	return $minutes;
}

function primaryApprovals(){
    return [
        [
            'name' => 'Processing',
            'class' => 'warning'
        ],
        [
            'name' => 'Approved',
            'class' => 'success'
        ],
        [
            'name' => 'Rejected',
            'class' => 'danger'
        ],
    ];
}

function uniqueCode($length,$prefix,$table,$field){
    $prefix_length = strlen($prefix);
    $max_id = DB::table($table)->count($field);
    $new = (int)($max_id);
    $new++;
    $number_of_zero = $length-$prefix_length-strlen($new);
    $zero = str_repeat("0", $number_of_zero);
    $made_id = $prefix.$zero.$new;
    return $made_id;
}

function uniqueCodeWithoutPrefix($length,$table,$field){
    $max_id = DB::table($table)->count($field);
    $only_id=$max_id[0]->$field;
    $new=(int)($only_id);
    $new++;
    $number_of_zero=$length-strlen($new);
    $zero=str_repeat("0", $number_of_zero);
    $made_id=$zero.$new;
    return $made_id;
}


function uniqueStringGenerator(){
    $s = 'abcdefghijklmnopqrstuvwxyz';
    $s = str_shuffle($s);
    $l = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $l = str_shuffle($l);
    $spc = '@_#-';
    $spc = str_shuffle($spc);
    $num = '0123456789';
    $num = str_shuffle($num);
    return $num.substr($spc,0,1).str_shuffle(substr($l,0,2).substr($s,0,2)).time();  
}

function ratingGenerate($totalScore='',$totalCount='')
{

	if ($totalScore > 0 && $totalCount >0) {
		//$totalMaxDecinalScore = $totalScore/ColumnCount('supplier_rattings');
		$averageRating = $totalScore/$totalCount;
	}else{
		$averageRating = 0.00;
	}

	$formatedVal = number_format($averageRating,2);
	$pieces = explode(".", $formatedVal);

	$star_list = str_repeat('<i class="fa fa-star rating-color" aria-hidden="true"></i>', number_format($pieces[0]));

	if ($pieces[1]>0) {
		$halfStart = '<i class="fa fa-star-half-o rating-color" aria-hidden="true"></i>';
	}else{
		$halfStart='';
	}

	$blankStar=(5-$formatedVal);

	$printRating='';

	for($j=1; $j <=$blankStar;$j++){
		$printRating .='<i class="fa fa-star"></i>';
	}
	
	return $star_list.''.$halfStart.''.$printRating;
}

function singleRatingGenerate($totalScore='',$totalCount='')
{

	if ($totalScore > 0 && $totalCount >0) {
		$averageRating = $totalScore/$totalCount;
	}else{
		$averageRating = 0.00;
	}

	$formatedVal = number_format($averageRating,2);
	$pieces = explode(".", $formatedVal);

	$star_list = str_repeat('<i class="fa fa-star rating-color" aria-hidden="true"></i>', number_format($pieces[0]));

	if ($pieces[1]>0) {
		$halfStart = '<i class="fa fa-star-half-o rating-color" aria-hidden="true"></i>';
	}else{
		$halfStart='';
	}

	$blankStar=(5-$formatedVal);

	$printRating='';

	for($j=1; $j <=$blankStar;$j++){
		$printRating .='<i class="fa fa-star"></i>';
	}

	return $star_list.''.$halfStart.''.$printRating;
}

function ColumnCount($table)
{
	$column = count(\Illuminate\Support\Facades\Schema::getColumnListing($table));
	return $removeExtraColumn=$column-10;
}


function supplierPaymentTerm(){

    return \App\Models\PmsModels\PaymentTerm::select('term','id')->get();
}

function supplierReceivedTerm(){
    return [
        'partial'=>'Partial Received',
        'full'=>'Full Received',
    ];
}

function supplierCriteria($supplier)
{
    $data = Illuminate\Support\Facades\Schema::getColumnListing('supplier_rattings');
    $deleteDefault = [0,1,9,10,11,12,13,14,15,16];
    $keys = array_diff(array_keys($data),$deleteDefault);
    $columns = [];
    $loop=0;
    foreach ($keys as $i=> $value){
        $loop++;
        $supplierData = (object)[
            'name' => ucwords(str_replace('_',' ',$data[$value])),
            'rating' => singleRatingGenerate($supplier->SupplierRatings()->sum($data[$value]),$supplier->SupplierRatings()->count()),
            'point' => number_format(($supplier->SupplierRatings()->sum($data[$value])/$supplier->SupplierRatings()->count()),2)
        ];
        $view = '<tr>
                                    <th>'.$loop.'</th>
                                    <td>'.$supplierData->name.'</td>
                                    <td>'.$supplierData->rating.'</td>
                                    <td>'.$supplierData->point.'</td>
                                </tr>';
        $columns[] = $view;
    }
    return implode(' ',$columns);
}

function supplierCriteriaColumns(){
    $data = \Illuminate\Support\Facades\Schema::getColumnListing('supplier_rattings');

    $deleteDefault = [0,1,9,10,11,12,13,14,15,16];
    $keys = array_diff(array_keys($data),$deleteDefault);
    $columns = [];
    foreach ($keys as $key=>$v){
        $columns[$key]= $data[$v];
    }

    return $columns;
}



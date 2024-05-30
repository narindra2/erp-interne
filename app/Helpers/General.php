<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Formula;


if (!function_exists('messaging')) {
    /**
     * Anchor Link
     *
     * Creates an anchor based on the local URL.
     *
     * @param	string	the URL
     * @param	string	the link title
     * @param	mixed	any attributes
     * @return	string
     */
    function messaging($email = '', $attributes = [])
    {

        $html_attributes = "";
        $attributes["data-act"] = "data-mail-to";
        if (is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $html_attributes .= ' ' . $key . '="' . $value . '"';
            }
        }

        return "<a  href='javascript:void(0)' $html_attributes>{$email}</a>";
    }
}

if (!function_exists('get_svg_icon')) {
    function get_svg_icon($path, $class = null, $svgClass = null)
    {
        if (strpos($path, 'media') === false) {
            $path = theme()->getMediaUrlPath() . $path;
        }

        $file_path = public_path($path);

        if (!file_exists($file_path)) {
            return '';
        }

        $svg_content = file_get_contents($file_path);

        if (empty($svg_content)) {
            return '';
        }

        $dom = new DOMDocument();
        $dom->loadXML($svg_content);

        // remove unwanted comments
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//comment()') as $comment) {
            $comment->parentNode->removeChild($comment);
        }

        // add class to svg
        if (!empty($svgClass)) {
            foreach ($dom->getElementsByTagName('svg') as $element) {
                $element->setAttribute('class', $svgClass);
            }
        }

        // remove unwanted tags
        $title = $dom->getElementsByTagName('title');
        if ($title['length']) {
            $dom->documentElement->removeChild($title[0]);
        }
        $desc = $dom->getElementsByTagName('desc');
        if ($desc['length']) {
            $dom->documentElement->removeChild($desc[0]);
        }
        $defs = $dom->getElementsByTagName('defs');
        if ($defs['length']) {
            $dom->documentElement->removeChild($defs[0]);
        }

        // remove unwanted id attribute in g tag
        $g = $dom->getElementsByTagName('g');
        foreach ($g as $el) {
            $el->removeAttribute('id');
        }
        $mask = $dom->getElementsByTagName('mask');
        foreach ($mask as $el) {
            $el->removeAttribute('id');
        }
        $rect = $dom->getElementsByTagName('rect');
        foreach ($rect as $el) {
            $el->removeAttribute('id');
        }
        $xpath = $dom->getElementsByTagName('path');
        foreach ($xpath as $el) {
            $el->removeAttribute('id');
        }
        $circle = $dom->getElementsByTagName('circle');
        foreach ($circle as $el) {
            $el->removeAttribute('id');
        }
        $use = $dom->getElementsByTagName('use');
        foreach ($use as $el) {
            $el->removeAttribute('id');
        }
        $polygon = $dom->getElementsByTagName('polygon');
        foreach ($polygon as $el) {
            $el->removeAttribute('id');
        }
        $ellipse = $dom->getElementsByTagName('ellipse');
        foreach ($ellipse as $el) {
            $el->removeAttribute('id');
        }

        $string = $dom->saveXML($dom->documentElement);

        // remove empty lines
        $string = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);

        $cls = array('svg-icon');

        if (!empty($class)) {
            $cls = array_merge($cls, explode(' ', $class));
        }

        $asd = explode('/media/', $path);
        if (isset($asd[1])) {
            $path = 'assets/media/' . $asd[1];
        }

        $output = "<!--begin::Svg Icon | path: $path-->\n";
        $output .= '<span class="' . implode(' ', $cls) . '">' . $string . '</span>';
        $output .= "\n<!--end::Svg Icon-->";

        return $output;
    }
}

if (!function_exists('theme')) {
    /**
     * Get the instance of Theme class core
     *
     * @return \App\Core\Adapters\Theme|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function theme()
    {
        return app(\App\Core\Adapters\Theme::class);
    }
}

if (!function_exists('check_null_array')) {
    function check_null_array($var)
    {
        $verif_null = 1;
        for($i=0;$i<count($var);$i++){
            if($var[$i]==null){
                $verif_null = $verif_null*0;
            }else{
                $verif_null = $verif_null*1;
            }
        }
        if($verif_null==0){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('get_inserted_line')) {
    function get_inserted_line($name, $date)
    {
        $kids = [];
        for($i=0;$i<count($name);$i++){
            if($name[$i]==null && $date[$i]==null){
                unset($name[$i]);
                unset($date[$i]);
            }
        }
        $kids["kids_fullname"] = $name;
        $kids["kids_birthdate_first"] = $date;
        return $kids;
    }
}

if (!function_exists('check_valid_line')) {
    function check_valid_line($name, $date)
    {
        $kids = [];
        for($i=0;$i<count($name);){
            if($name[$i]==null && $date[$i]==null){
                unset($name[$i]);
                unset($date[$i]);
                $name = array_values($name);
                $date = array_values($date);
            }else{
                $i++;
            }
        }
        $kids["kids_fullname"] = $name;
        $kids["kids_birthdate_first"] = $date;
        return $kids;
    }
}

function checkKids($names = [], $birthDates = [])
{
    for ($i = 0; $i < count($names);) {
        if ($names[$i] == null && $birthDates[$i] == null) {
            unset($birthDates[$i]);
            unset($names[$i]);
            $birthDates = array_values($birthDates);
            $names = array_values($names);
        }
        else if ($names[$i] == null && $birthDates[$i] != null)
        {
            $message = "Absence du nom de l'enfant";
            die(json_encode(["success" => false, "message" => $message ]));
        }else if($names[$i] != null && $birthDates[$i] == null){
            // . $i+1
            $message = "Absence de date de naissance de l'enfant";
            die(json_encode(["success" => false, "message" => $message ]));
        }
        else {
            $i++;
        }
    }
    return "";
}

if (!function_exists('util')) {
    /**
     * Get the instance of Util class core
     *
     * @return \App\Core\Adapters\Util|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function util()
    {
        return app(\App\Core\Adapters\Util::class);
    }
}

if (!function_exists('bootstrap')) {
    /**
     * Get the instance of Util class core
     *
     * @return \App\Core\Adapters\Util|\Illuminate\Contracts\Foundation\Application|mixed
     * @throws Throwable
     */
    function bootstrap()
    {
        $demo      = ucwords(theme()->getDemo());
        $bootstrap = "\App\Core\Bootstraps\Bootstrap$demo";

        if (!class_exists($bootstrap)) {
            abort(404, 'Demo has not been set or ' . $bootstrap . ' file is not found.');
        }

        return app($bootstrap);
    }
}

if (!function_exists('assetCustom')) {
    /**
     * Get the asset path of RTL if this is an RTL request
     *
     * @param $path
     * @param  null  $secure
     *
     * @return string
     */
    function assetCustom($path)
    {
        // Include rtl css file
        if (isRTL()) {
            return asset(theme()->getDemo() . '/' . dirname($path) . '/' . basename($path, '.css') . '.rtl.css');
        }

        // Include dark style css file
        if (theme()->isDarkModeEnabled() && theme()->getCurrentMode() !== 'default') {
            $darkPath = str_replace('.bundle', '.' . theme()->getCurrentMode() . '.bundle', $path);
            if (file_exists(public_path(theme()->getDemo() . '/' . $darkPath))) {
                return asset(theme()->getDemo() . '/' . $darkPath);
            }
        }

        // Include default css file
        return asset(theme()->getDemo() . '/' . $path);
    }
}

if (!function_exists('isRTL')) {
    /**
     * Check if the request has RTL param
     *
     * @return bool
     */
    function isRTL()
    {
        return (bool) request()->input('rtl');
    }
}




/** get the value of key in array
 * @param array
 * @param string
 * @retrun mixte
 */
if (!function_exists('get_array_value')) {
    function get_array_value($array = [], $key = "", $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }else{
            return $default !== null ? $default  : null;
        }
    }
}



/**
 * Get a file type
 * @param string
 */
if (!function_exists('get_file_type')) {
    function get_file_type($extension = "")
    {
        $file_type = file_type();
        foreach ($file_type as $type => $extensions) {
            if (in_array(strtolower($extension), $extensions)) {
                return $type;
            }
        }
        return "file";
    }
}

/**
 * All file type
 * @param string
 */
if (!function_exists('file_type')) {
    function file_type($type = "")
    {
        $types  = [
            "image" =>  ['.jpg',  '.png', '.jpeg', '.webp'],
            "video" =>  ['.wmv', '.avi', '.mov', '.3gp', '.ts', '.mp4', '.flv', ".mkv"],
            "audio" =>  ['.mp3', '.ogg', '.wav', '.mid', '.midi', ".wma", ".cda", ".wpl"],
            "doc"   =>  ['.txt', ".doc", ".php", ".xlsx", ".pptx", ".pdf", ".odt", ".wpd"],
            "archive"   =>  ['.rar', ".zip", ".iso"],
        ];
        if ($type) {
            return  get_array_value($types, $type);
        }
        return $types;
    }
    //png,jpg,jpeg,webp,pdf,avi,mov,3gp,mp4,flv,mkv,mp3,wav,txt,doc,xlsx,pptx,rar,zip,iso
}
/**
 *@param string
 * @param string
 */
if (!function_exists('app_check_secure')) {
    function app_check_secure($string = "", $data_encrypted = null, $from_ajax = false)
    {
        $is_secured = app_secure_encrypt($string) === $data_encrypted;
        if (!$is_secured) {
            if ($from_ajax) {
                die(json_encode(["success" => false, "message" => " Data not secured"]));
            } else {
                return abort(405);
            }
            return $is_secured;
        }
    }
}
/**
 *@param string
 * @param string
 */
if (!function_exists('app_secure_encrypt')) {
    function app_secure_encrypt($string = "")
    {
        return md5(md5("app-encrypt-" . $string));
    }
}
/**
 *@param string
 * @param string
 */
if (!function_exists('app_secure_input')) {
    function app_secure_input($name = "_data_secure", $value = "")
    {
        return "<input type ='hidden' name = '$name' value ='$value' />";
    }
}


/**
 * Formater number ex. 1000 => 1k , 10000 => 1M
 * @param int
 * @param string $unit
 * @retrun string
 */
if (!function_exists('format_to_KMG')) {
    function format_to_KMG($number = 0, $unit = "")
    {
        $number_foramted = 0;

        if ($number >= 1000000000) {
            $number_foramted = round($number / 1000000000, 1) . 'G';
        } elseif ($number >= 1000000) {
            $number_foramted =  round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            $number_foramted =  round($number / 1000, 1) . 'K';
        } else {
            $number_foramted = $number;
        }
        if (strval($number) > 1 && $unit) {
            $unit = Str::of($unit)->plural();
        }
        return $number_foramted . " " . $unit;
    }
}

/**
 * Get file uri
 * @param string
 * @param string
 * @retrun path
 */
if (!function_exists('get_file_uri')) {
    function get_file_uri($file = "", $path = "", $storage = "public")
    {
        if ($storage == "public") {
            return asset($path) . "/" . $file;
        }
    }
}


/**
 *  Return the image format ex : "100x50" => ["width" => 100, "height" => 50];
 *  @param string
 *  @retrun Array
 */
if (!function_exists('get_format_image')) {
    function get_format_image($format = "")
    {
        $specific_format = explode("x", $format);
        $width = intval($specific_format[0]);
        $height = intval($specific_format[1]);
        if ($width && $height) {
            return ["width" => $width, "height" => $height];
        }
    }
}

/**
 * Uploader of  file
 * @param Illuminate\Http\Request
 * @param string
 * @return array
 */
if (!function_exists('upload')) {
    function upload($file, $path = null, $public = "", $compress = ["folder" => "", "format" => null])
    {
        $fileToCompress = $file;
        $extension = $file->extension();
        $size = $file->getSize(); // kB;
        $file_type = get_file_type("." . $extension);
        $file_info = [];
        $file_info["success"] = false;
        if (!$file) {
            return $file_info;
        }
        $path =  $path ? $path : "uploads";
        $real_storage = ($public === "public") ? public_path($path) : storage_path("app/public/" . $path);
        $name =  $file_type . "-" . Str::uuid() . '.' . $extension;
        $file->move($real_storage, $name);
        $file_info["success"] = true;

        
        if ($name) {
            $file_info["url"]  = !$public ? $path . "/" . $name :  "storage/" . $path . "/" . $name;
            $file_info["src"]  = $real_storage . "/" . $name;
            $file_info["type"] = $file_type;
            $file_info["size"] = $size;
            $file_info["name"] = $name;
            $file_info["extension"] = $extension;
            $file_info["originale_name"] = $file->getClientOriginalName();
            if (get_array_value($compress, "format")) {
                // dd($file_info['src']);
                $img = Image::make($file_info['src'])->fit(get_array_value($compress, "format"));
               // $real_storage . "-" .. "/" . $name//
                $img->save($real_storage . "/" .  get_array_value($compress, "format") . "-" . $name);
               
            }
            return $file_info;
        }
    }
}
/**
 * Uploader of  file
 * @param Illuminate\Http\Request
 * @param string
 * @return array
 */
if (!function_exists('move')) {
    function move($file, $path = null, $storage = "public")
    {
        return upload($file, $path, $storage);
    }
}


/**
 * prepare a anchor tag for any js request
 *
 * @param string $title
 * @param array $attributes
 * @return html link of anchor tag
 */
if (!function_exists('js_anchor')) {

    function js_anchor($title = '', $attributes = [])
    {
        $title = (string) $title;
        $html_attributes = "";

        if (is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $html_attributes .= ' ' . $key . '="' . $value . '"';
            }
        }

        return '<a href="javascript:void(0)"' . $html_attributes . '>' . $title . '</a>';
    }
}


/**
 * prepare a anchor tag for modal
 *
 * @param string $url
 * @param string $title
 * @param array $attributes
 * @return html link of anchor tag
 */
if (!function_exists('modal_anchor')) {

    function modal_anchor($url, $title = '', $attributes = [])
    {
        if (get_array_value($attributes, "data-drawer")) {
            $attributes["data-act"] = "ajax-drawer";
        } else {
            $attributes["data-act"] = "ajax-modal";
        }

        if (get_array_value($attributes, "data-modal-title")) {
            $attributes["data-title"] = get_array_value($attributes, "data-modal-title");
        } else {
            $attributes["data-title"] = get_array_value($attributes, "title");
        }
        $attributes["data-action-url"] = $url;

        return js_anchor($title, $attributes);
    }
}

/**
 * prepare a anchor tag for ajax request
 *
 * @param string $url
 * @param string $title
 * @param array $attributes
 * @return html link of anchor tag
 */
if (!function_exists('ajax_anchor')) {

    function ajax_anchor($url, $title = '', $attributes = [])
    {
        $attributes["data-act"] = "ajax-request";
        $attributes["data-action-url"] = $url;
        return js_anchor($title, $attributes);
    }
}
if (!function_exists('anchor')) {
    /**
     * Anchor Link
     *
     * Creates an anchor based on the local URL.
     *
     * @param	string	the URL
     * @param	string	the link title
     * @param	mixed	any attributes
     * @return	string
     */
    function anchor($uri = '', $title = '', $attributes = [])
    {

        $uri = url($uri);
        $title = (string) $title;
        $html_attributes = "";
        if (1) {
            $attributes['data-bs-toggle'] = "tooltip";
            $attributes['data-bs-custom-class'] = "tooltip-dark";
            $attributes['data-bs-placement'] = "left";
        }
        if (is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $html_attributes .= ' ' . $key . '="' . $value . '"';
            }
        }

        return "<a href='$uri' $html_attributes>$title</a>";
    }
}
if (!function_exists('mailing')) {
    /**
     * Anchor Link
     *
     * Creates an anchor based on the local URL.
     *
     * @param	string	the URL
     * @param	string	the link title
     * @param	mixed	any attributes
     * @return	string
     */
    function mailing($email = '', $attributes = [])
    {

        $html_attributes = "";
        $attributes["data-act"] = "data-mail-to";
        if (is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $html_attributes .= ' ' . $key . '="' . $value . '"';
            }
        }

        return "<a  href='javascript:void(0)' $html_attributes>{$email}</a>";
    }
}
if (!function_exists('inputs_filter_datatable')) {

    function inputs_filter_datatable($filters = [])
    {
        $ids = [];
        foreach ($filters as $filter) {
            $ids[] = get_array_value($filter, "name");
        }
        return $ids;
    }
}
/** Convert to DB date format */
if (!function_exists('to_date')) {

    function to_date($date = "",$current_format ='d/m/Y' , $time = false)
    {
        $db_format = "Y-m-d"; // mysql defaut format date
        if ($time) {
            return (DateTime::createFromFormat($current_format, str_replace(" ", "", $date)))->format("$db_format H:m:s");
        }
        return (DateTime::createFromFormat($current_format, str_replace(" ", "", $date)))->format($db_format);
    }
}
if (!function_exists('to_date_time')) {

    function to_date_time($date = "",$current_format = 'd/m/Y')
    {
        return to_date($date,$current_format,true);
    }
}
if (!function_exists('file_sisze')) {

    function file_sisze($size = 0)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
if (!function_exists('row_id')) {
    function row_id($table_name = "table", $id = 0)
    {
        return $table_name . "_row_" . $id;
    }
}



if (!function_exists('project_path_file')) {
    function project_path_file($project_id = "", $file = null)
    {
        if ($file) {
            return storage_path() . "/" . "app/public/project_files/$project_id/$file";
        }
        return storage_path() . "/" . "app/public/project_files/$project_id";
    }
}
if (!function_exists('project_file_url')) {
    function project_file_url($project_id = 0, $file = null)
    {
        if ($file) {
            return asset("storage/project_files/$project_id/$file");
        }
        return asset("storage/project_files/$project_id/");
    }
}
if (!function_exists('invoice_item_num')) {
    function invoice_item_num($invoice_id, $invoice_item_id)
    {
        return "invoice-{$invoice_id}-item-{$invoice_item_id}";
    }
}


if (!function_exists('convert_date')) {
    function convert_date($date = "")
    {
        if (!$date) {
            return null;
        }
        return DateTime::createFromFormat('Y-m-d', $date)->format('d/m/Y');
    }
}

if (!function_exists('getDateInDateRange')) {
    function getDateInDateRange($date) {
        try {
            $date = explode("-", $date);
            $data = [to_date($date[0]), to_date($date[1])];
            return $data;
        }
        catch (Error $e) {
            return [Carbon::today()->subMonth(), Carbon::today()];
        }
    }
}

if (!function_exists('str_limite')) {
    function str_limite($string ,$limite = 100, $end = " ...")
    {
        return Str::limit($string,$limite,$end);
    }
}
/**
 * $value is the key in column in collection
 * $text is the ication or mabel in column in collection
 * 
 */
if (!function_exists('to_dropdown')) {
    function to_dropdown($collections ,$value = "id", $text = "name")
    {
        $dropdowns = [];
        foreach ($collections as $item) {
            $dropdowns[] = ["value" =>  $item->$value  ?? "null"  , "text" => $item->$text  ?? "null"] ;
        }
        return $dropdowns ;
    }
}


if (!function_exists('convert_database_date')) {
    function convert_database_date($date)
    {
        return date('d/m/Y', strtotime($date));
    }
}
if (!function_exists('convert_date_to_database_date')) {
    function convert_date_to_database_date($date)
    {
        $date = str_replace('/', '-', $date);
        return date('Y-m-d', strtotime($date));
    }
}
if (!function_exists('convert_to_real_time_humains')) {
    function convert_to_real_time_humains($dateTime = "" ,$format ="d-m-Y" ,$with_time = true)
    {
        if (!$dateTime) {
          return null; 
        }
        $humains_date = null;
        $date = Carbon::make( $dateTime);
        if ($date->isToday()) {
            return $date->diffForHumans();
        }elseif ($date->isYesterday()) {
            $humains_date = "Hier" .($with_time ? " à"  : "");
        }elseif ($date->format($format) ==  Carbon::tomorrow()->format($format)) {
            $humains_date = "Démain" .($with_time ? " à partir"  : "");;
        }else{
            return $date->format($format .($with_time ? " H:m":""));
        }
        return $humains_date ." " .($with_time  ? $date->format("H:m") : "");
    }
}
if (!function_exists('convertMinuteToHour')) {
function convertMinuteToHour($minutes, $useNegation = false, $format = '%02d:%02d')
    {
        $hours = floor($minutes / 60);
        $minutes = ($minutes % 60);
        return sprintf($format, $hours, $minutes);
    }
}
/**Convert seconds to days , hours,  minutes  and seconds in format D:H:M:S */
if (!function_exists('seconds_to_dhms')) {
    function seconds_to_dhms($ss) {
        $dt1 = new DateTime("@0");
        $dt2 = new DateTime("@$ss");
        return $dt1->diff($dt2)->format('%D:%H:%I:%S');
    }
}
if (!function_exists('monthList')) {
    function monthList($selected = 0) {
        
        return [
            ["text" => "Janvier", "value" => "1" ,"selected" => ($selected == 1 || $selected == 0)],
            ["text" => "Février", "value" => "2","selected" => ($selected == 2)],
            ["text" => "Mars", "value" => "3","selected" => ($selected == 3)],
            ["text" => "Avril", "value" => "4","selected" => ($selected == 4)],
            ["text" => "Mai", "value" => "5","selected" => ($selected == 5)],
            ["text" => "Juin", "value" => "6","selected" => ($selected == 6)],
            ["text" => "Juillet", "value" => "7","selected" => ($selected == 7)],
            ["text" => "Août", "value" => "8","selected" => ($selected == 8)],
            ["text" => "Septembre", "value" => "9","selected" => ($selected == 9)],
            ["text" => "Octobre", "value" => "10","selected" => ($selected == 10)],
            ["text" => "Novembre", "value" => "11","selected" => ($selected == 11)],
            ["text" => "Décembre", "value" => "12","selected" => ($selected == 12)],
        ];
    }
}
if (!function_exists('yearList')) {
    function yearList($get_last_year = 4 , $selectCurentYear = true) {
        $years = [];
        $current  = Carbon::now()->year;
        $years[]=  ["text" => $current, "value" => $current , "selected" => $selectCurentYear];
        for ($i = 1 ; $i < $get_last_year  ; $i++ ) {
            $years[]=  ["text" =>  $current - $i, "value" => $current - $i];
        }
        return  $years;
    }
}
if (!function_exists('getDateHuman')) {
    function getDateHuman($date) {
        try {
            $date = Carbon::make($date);
            if ($date->isToday())   return $date->diffForHumans();
            return $date->format("d-m-Y");
        }
        catch (Error $e) {
            return "";
        }
    }
}
if (!function_exists('getNumberDayInMonth')) {
    function getNumberDayInMontWithoutWeekends ($month,$year ){
        $fist_date_in_month = Carbon::make("$year-$month-01");
        $nb_days_in_month =  $fist_date_in_month->daysInMonth;
        $end_date_in_month = Carbon::make("$year-$month-$nb_days_in_month"); //  ex : "30-06-2022" or "31-07-2022" or "29-02-2022" or "28-02-2022"
        return ($fist_date_in_month->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, $end_date_in_month)) +1;
    }
}


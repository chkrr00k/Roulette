<?php
//made by chkrr00k
	class Gun implements JsonSerializable{
    	private $bullets;
        private $loaded;

        public function __construct($input) {
        	$tmp = json_decode($input, true);
        	$this->bullets = $tmp["bullets"];
            $this->loaded = $tmp["loaded"];
        }
        public function jsonSerialize(){
        	return get_object_vars($this);
        }
        public function trigger(){
        	if($this->loaded > count($this->bullets) - 1){
            	$this->loaded = -1;
            }
            return $this->bullets[$this->loaded++];
        }
        
        public static function load($file){
        	$f = fopen($file, "r") or die("Wrong file name!");
            $content = fread($f, filesize($file));
            fclose($f);
            
            return new Gun($content);
        }
        public static function save($file, $gun){
        	$f = fopen($file, "w") or die("Wrong file name!");
            fwrite($f, json_encode($gun));
            fclose($f);
        }
        public static function generate($nbullets, $nrounds){
        	$bullets = array_merge(array_fill(0, $nbullets, 1), array_fill(0, $nrounds - $nbullets, 0));
            shuffle($bullets);
            return new Gun(
            	json_encode(
                	array(
                    	"bullets" => $bullets,
                        "loaded" => 0
                    )
                )
            );
        }
    }
	
    $fileName = "gun.txt";

    
    if(isset($_GET["reload"])){
    	if(isset($_GET["rounds"]) && is_numeric($_GET["rounds"]) && $_GET["rounds"] > 0 && $_GET["rounds"] < 7){
        	$g = Gun::generate($_GET["rounds"], 6);
        }else{
    		$g = Gun::generate(1, 6);
        }
        Gun::save($fileName, $g);
    }elseif(isset($_GET["shoot"])){
    	$g = Gun::load($fileName);
    	$res = $g->trigger();
        switch($res){
          case 1:
              echo "True";
              break;
          case 0: 
          default:
          	  echo "False";
              break;
        }
   		Gun::save($fileName, $g);
    }
?>
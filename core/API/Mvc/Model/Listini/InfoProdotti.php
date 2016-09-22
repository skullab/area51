<?php

namespace Thunderhawk\API\Mvc\Model\Listini;
use Thunderhawk\API\Mvc\Model;
class InfoProdotti extends Model{
	public $id;
	public $nome;
	public $formato;
	public $prodotto_larghezza;
	public $prodotto_altezza;
	public $prodotto_profondita;
	public $prodotto_diametro;
	public $imballo_larghezza;
	public $imballo_altezza;
	public $imballo_profondita;
	public $pezzi_cartone;
	public $pallettizzazione_strati;
	public $pallettizzazione_cartoni;
	public $peso_singolo;
	public $peso_totale;
	public $ean;
	public $itf;
	public $ps_product_id;
	public $pr_listini_versioni_id;
	
	protected function onInitialize(){
		$this->setAsVendorModel();
		$this->belongsTo('ps_product_id','Thunderhawk\API\Mvc\Model\Products\Product','id_product',array(
				'alias' => 'prodotto',
				'reusable' => true
		));
		$this->belongsTo('pr_listini_versioni_id',__NAMESPACE__.'\ListiniVersioni','id',array(
				'alias' => 'versione',
				'reusable' => true
		));
	}
}
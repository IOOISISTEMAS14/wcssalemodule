<?php
class Venda_model extends CI_model{
	function fetch_data($query){
	    $this->db->select("*");
		$this->db->from("produto");
		if($query != '')
		{
			$this->db->like('nome',$query);
			$this->db->or_like('id',$query);
			$this->db->or_like('descricao',$query);
			$this->db->or_like('preco',$query);  
		}
		$this->db->order_by('id','DESC');
		return $this->db->get();
	}	
	function insert_data($query,$query2){
		if($_SESSION['venda']==0){
		//$query2=serialize($query2);
		$this->db->from("produto");
		$this->db->like('id',$query);
		$prod=$this->db->get();
		foreach($prod->result() as $row)
        {
			$pprod=serialize($row->preco);
		}
		$query0[0]=$query;
		$query1 = serialize($query0);
		$query3[0]=$query2;
		$query4 = serialize($query3);	
		$this->db->from("venda_itens");
		$venda_itens =array (
			 "prods" => $query1,
			 "qtdprod" => $query4,
			 "preco" => $pprod
		);
		$this->db->trans_start();
		$this->db->insert('venda_itens',$venda_itens);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		foreach($_SESSION['usuario'] as $row){
			$usr = $row->id;
			} 
		$this->db->from("venda");
		$venda = array(
			"idu" => $usr,
			"idvvi" => $insert_id
		);
		$this->db->trans_start();
		$this->db->insert('venda',$venda);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		$_SESSION['venda'] = $insert_id;
	    $this->db->select("*");
		$this->db->from("produto");
		if($query != '')
		{
			$this->db->like('id',$query);
		}
		return $this->db->get();   
	   }
	   else{
		   $this->db->from("venda");
		   $this->db->where('idv',$_SESSION['venda']); 
		   $v_atual=$this->db->get();
		   foreach($v_atual->result() as $row)
            {
			 $viatual=$row->idvvi;
			} 
		   $this->db->from("venda_itens");
		   $this->db->where('idvvi',$viatual);
		   $viatuals=$viatual;
		   $viatual=$this->db->get();
		   foreach($viatual->result() as $row)
		   {
			$viatual2 = (unserialize($row->prods));
			$viatualqtd = (unserialize($row->qtdprod));
		   }
		   if($viatual2){
		   if(in_array($query,$viatual2)){}
		   else{
		   $cont = count($viatual2);	 	      	
		   $viatual2[$cont] = $query;
		   $viatualqtd[$cont]=$query2;
		   }
		   $viatual3 = serialize($viatual2);
		   $viatualqtd2=serialize($viatualqtd);
		   $venda = array(
			   "prods"=>$viatual3,
			   "qtdprod"=>$viatualqtd2
		   );
		   $this->db->from("venda_itens");
		   $this->db->like('idvvi',$viatuals);
		   $this->db->update('venda_itens',$venda);
		   $this->db->select("*"); 
		   $this->db->from("produto");
		   if($query != '')
		   {
			$this->db->like('id',$query);
		   }
		   return $this->db->get(); 
		   }
		   else{
		   $viatual2[0] = $query;
		   $viatualqtd[0]=$query2;
		   $viatual3 = serialize($viatual2);
		   $viatualqtd2=serialize($viatualqtd);
		   $venda = array(
			   "prods"=>$viatual3,
			   "qtdprod"=>$viatualqtd2
		   );
		   $this->db->from("venda_itens");
		   $this->db->like('idvvi',$viatuals);
		   $this->db->update('venda_itens',$venda);
		   $this->db->select("*"); 
		   $this->db->from("produto");
		   if($query != '')
		   {
			$this->db->like('id',$query);
		   }
		   return $this->db->get(); 
		   }   	
	  }
	}
 function verify_v($id){
	$retorno[0]=0;
	$retorno[1]=0;  
	$this->db->from("venda");
	$this->db->where('idv',$_SESSION['venda']); 
	$v_atual=$this->db->get();
	foreach($v_atual->result() as $row)
	 {
	  $viatual=$row->idvvi;
	 } 
	$this->db->from("venda_itens");
	$this->db->where('idvvi',$viatual);
	$viatual=$this->db->get();
	foreach($viatual->result() as $row){
	$viatual2=unserialize($row->prods);
	}
	if($viatual2){
	$cont = count($viatual2);
	for($i=0;$i<$cont;$i++){
		if($id == $viatual2[$i]){	
		   foreach($viatual->result() as $row){
		   $qtdatual2=unserialize($row->qtdprod);
		   $qtdatual3=$qtdatual2[$i];
			}	
		   $retorno[0]= $qtdatual3;
		   $retorno[1]="checked"; 	
		   return $retorno;
		   }
		}
	}	
	return $retorno;	
   } 
   function clean(){
	    $this->db->from("venda_itens");
		$venda_itens =array (
			 "prods" => '',
			 "qtdprod" => '',
			 "preco" => ''
		);
		$this->db->trans_start();
		$this->db->insert('venda_itens',$venda_itens);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		foreach($_SESSION['usuario'] as $row){
			$usr = $row->id;
			} 
		$this->db->from("venda");
		$venda = array(
			"idu" => $usr,
			"idvvi" => $insert_id
		);
		$this->db->trans_start();
		$this->db->insert('venda',$venda);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		$_SESSION['venda'] = $insert_id;
   }
}
?>
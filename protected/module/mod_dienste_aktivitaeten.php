<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Modus und Ansicht
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data['modus'] = CSiteManager::getMode();
$data['view'] = C2C_Modus($data['modus']);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Das Objekt
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$Mitglied = new CMitglied();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Filterung/Sortierung
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data['fltr1'] = ((isset($_GET['fltr1']))?(1):(0));
$data['fltr2'] = ((isset($_GET['fltr2']))?((int)$_GET['fltr2']):(0));
$data['sort'] = ((isset($_GET['sort']))?((int)$_GET['sort']):(0));

$data['fs_string'] = '';
$data['fs_string'] .= (($s = $data['fltr1'])?('&amp;fltr1='.$s):(''));
$data['fs_string'] .= (($s = $data['fltr2'])?('&amp;fltr2='.$s):(''));
$data['fs_string'] .= (($s = $data['sort'])?('&amp;sort='.$s):(''));

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Übersicht
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(VIEW_LIST == $data['view'])
{
	//--------------------------------------------------------------------------------------------------------------------
	// Auswahl
	//--------------------------------------------------------------------------------------------------------------------
	$query = 'SELECT a.athlet_id '.
	         'FROM ((athleten a INNER JOIN _v2_mitglieder_aklagruppe makl ON a.athlet_id=makl.athlet_id) '.
	         'INNER JOIN athleten_mitglieder am ON a.athlet_id=am.athlet_id) '.
	         'INNER JOIN _v0_mitglieder_alter malt ON a.athlet_id=malt.athlet_id ';

	//--------------------------------------------------------------------------------------------------------------------
	// Filterung
	//--------------------------------------------------------------------------------------------------------------------
	$query_where = array();

	if(!$data['fltr1']) {$query_where[] = 'am.ausblenden=0';}
	if($data['fltr2']) {$query_where[] = 'makl.aklagruppe'.((4==$data['fltr2'])?(' IS NULL'):('='.$data['fltr2']));}

	foreach($query_where as $i => $clause) {$query .= (($i)?(' AND '):(' WHERE ')).$clause;}

	//--------------------------------------------------------------------------------------------------------------------
	// Sortierung
	//--------------------------------------------------------------------------------------------------------------------
	switch($data['sort'])
	{
		case 1: $query .= ' ORDER BY am.lastupdate DESC, a.nachname, a.vorname'; break;
		case 2: $query .= ' ORDER BY a.nachname, a.vorname'; break;
		default: $query .= ' ORDER BY am.lastlogin DESC, a.nachname, a.vorname'; break;
	}

	//--------------------------------------------------------------------------------------------------------------------
	// Abfrage
	//--------------------------------------------------------------------------------------------------------------------
	$data['stichtag'] = S2S_Datum_MySql2Deu(CDBConnection::getInstance()->getStichtag());

	$data['mitglied_array'] = array();
	if(!$result = mysql_query($query)) {throw new Exception(mysql_error(CDBConnection::getDB()));}
	while($row = mysql_fetch_row($result)) {$data['mitglied_array'][] = new CMitglied($row[0]);}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// RETURN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

return new CTemplateData($data);
?>
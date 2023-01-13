<?php
#
# This file is part of the STAR Shift Signup UI package
#
# This program is free software; you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the
# Free Software Foundation; either version 2, or (at your option) any
# later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
#

function updatetraining_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $person = intval($_POST['id']);

    //$mem = $db->Query('SELECT m.Id, m.FirstName, m.LastName, i.InstitutionName FROM `starweb`.members m, `starweb`.institutions i WHERE m.Id = '.$person.' AND m.InstitutionId = i.Id LIMIT 1');    
    //$mem = $mem[0];

    $members = pnb_get_active_members();
    $institutions = pnb_get_active_institutions();
    $mem = array( 'Id' => $person, 'FirstName' => $members[$person]['fields'][1], 'LastName' => $members[$person]['fields'][3],
	'InstitutionName' => $institutions[ $members[$person]['fields'][17] ]['fields'][1] );

    $trn = $db->Query('SELECT * FROM `ShiftSignup`.ShiftTraining WHERE personID = '.$person.' ORDER BY shiftTypeID ASC');
    $ptrain = array();
    foreach( $trn as $k => $v ) {
			$ptrain[$v['shiftTypeID']] = $v;
    }

//		print_r($ptrain); exit;

    $utrain = array();

    for ($i = 1; $i <= 4; $i++) {
        $utrain[$i]['trainingComplete'] = intval($_POST['trainingComplete'][$i]);
        $utrain[$i]['beginTime'] = $_POST['beginTime'][$i];
        $utrain[$i]['isPending'] = intval($_POST['isPending'][$i]);
        $utrain[$i]['locked'] = intval($_POST['locked'][$i]);
				$utrain[$i]['comments'] = $_POST['comments'][$i];

        if ($utrain[$i]['locked'] != 1 || $i == 4 ) {
            if ($utrain[$i]['trainingComplete'] == 1 && empty($ptrain[$i])) {
								$q = 'INSERT INTO `ShiftSignup`.ShiftTraining (shiftTypeID, personID, beginTime, isPending, comments) VALUES ('.$i.','.$person.',"'.$utrain[$i]['beginTime'].'",'.$utrain[$i]['isPending'].', "'.$db->Escape($utrain[$i]['comments']).'")';
                $res = $db->Query($q);
            } else if (!empty($ptrain[$i]) && $utrain[$i]['trainingComplete'] == 0) {
                $res = $db->Query('DELETE FROM `ShiftSignup`.ShiftTraining WHERE personID = '.$person.' AND shiftTypeID = '.$i);
            } else if (!empty($ptrain[$i]) && $utrain[$i]['trainingComplete'] == 1 && $ptrain[$i]['beginTime'] != $utrain[$i]['beginTime']) {
                $res = $db->Query('UPDATE `ShiftSignup`.ShiftTraining SET beginTime = "'.$utrain[$i]['beginTime'].'" WHERE personID = '.$person.' AND shiftTypeID = '.$i);
            } else if ( $ptrain[$i]['isPending'] != $utrain[$i]['isPending'] || $ptrain[$i]['comments'] != $utrain[$i]['comments'] ) {
                $res = $db->Query('UPDATE `ShiftSignup`.ShiftTraining SET isPending = "'.$utrain[$i]['isPending'].'", comments = "'.$utrain[$i]['comments'].'" WHERE personID = '.$person.' AND shiftTypeID = '.$i);
            }
				}
    }
    $message = '<div class="edit_status_message">Records updated at '.date('r').'</div>';


    $trn = $db->Query('SELECT * FROM `ShiftSignup`.ShiftTraining WHERE personID = '.$person.' ORDER BY shiftTypeID ASC');
    $train = array();                                                
    if (!empty($trn) ) {
        foreach ($trn as $k => $v) {
            $train[$v['shiftTypeID']] = $v;
        }
    }

    $shifts = $db->Query('SELECT * FROM `ShiftSignup`.Shifts WHERE personID = '.$person);
    $locked = array();

    foreach($shifts as $k => $v) {
        if ($v['shiftTypeID'] == 1) { $locked[1] = 1; }
        if ($v['shiftTypeID'] == 2) { $locked[2] = 1; }
        if ($v['shiftTypeID'] == 3 || $v['shiftTypeID'] == 4) { $locked[3] = 1; }
    }

    $tpl->set('mem', $mem);
    $tpl->set('train', $train);
    $tpl->set('lock', $locked);

    $tpl->set('message', $message);
    $tpl->set_file('edittraining.tpl.php');

}



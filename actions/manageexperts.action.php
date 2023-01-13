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

function manageexperts_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $cat = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExpertiseCategories WHERE 1 ORDER BY catWeight ASC');    
    $refs = array();
    $list = array();
    if (!empty($cat) && is_array($cat)) {
        foreach($cat as $k => $v) {
            $thisref = &$refs[ $v['id'] ];
            $thisref['id'] = $v['id'];
            $thisref['w'] = $v['catWeight'];
            $thisref['parentId'] = $v['parentId'];
            $thisref['name'] = $v['catName'];
            if ($v['parentId'] == 0) {
                $list[ $v['id'] ] = &$thisref;
            } else {
                $refs[ $v['parentId'] ]['children'][ $v['id'] ] = &$thisref;
            }
        }
    }
    $experts = get_expert_members();
    
    $m = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExperts WHERE 1 ORDER BY exLastName ASC');    
    //$m = $db->Query('SELECT a.*, b.ExpertCredit as ecred FROM `ShiftSignup`.`ShiftExperts` a, `starweb`.`members` b WHERE a.exID = b.ID ORDER BY a.exLastName ASC');
    foreach($m as $k => $v) {                                                                                                                                           
        if ( !empty($experts[ $v['exID'] ]['ExpertCredit']) ) {                                                                                                         
            $m[$k]['ExpertCredit'] = intval($experts[ $v['exID'] ]['ExpertCredit']);                                                                                    
            $m[$k]['ecred'] = intval($experts[ $v['exID'] ]['ExpertCredit']);                                                                                           
        } else {                                                                                                                                                        
            $m[$k]['ExpertCredit'] = 0;                                                                                                                                 
            $m[$k]['ecred'] = 0;                                                                                                                                        
        }                                                                                                                                                             
    } 

    $tpl->set('cat', $list);
    $tpl->set('experts', $m);
    $tpl->set_file('manageexperts.tpl.php');
}
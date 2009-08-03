<?php

!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');

class RecommendThreadModel extends BaseModel {
    
    public function count(){
        $result = $this->db->result_first("SELECT COUNT(*) FROM {$this->dbTablepre}forumrecommend");
        if(!($result)){
            return false;
        }else{
            return $result;
        }
    }
    
    public function findall(){
        global $dateformat, $timeformat,$timestamp,$timeoffset;
        $options = $this->options;
        $this->options = array();
        $sql = "SELECT fr.*, f.name, t.dateline, t.lastpost, t.lastposter FROM {$this->dbTablepre}forumrecommend fr
                     LEFT JOIN {$this->dbTablepre}forums f ON f.fid = fr.fid
                     LEFT JOIN {$this->dbTablepre}threads t ON t.tid = fr.tid
                     ORDER BY {$options['order']} DESC LIMIT {$options['limit']}";
        $recommendlist = array();
        $cache = FileCache::getInstance();
        if(!($recommendlist = $cache->load($sql))){
            $query = $this->db->query($sql);
            while($recommend = $this->db->fetch_array($query)) {
                if(($recommend['expiration'] && $recommend['expiration'] > $timestamp) || !$recommend['expiration']) {
                    //$recommend['subject'] = cutstr($recommend['subject'],$hack_cut_str);
                    $recommend['subject'] = htmlspecialchars($recommend['subject']);
                    $recommend['dateline'] = gmdate("$dateformat $timeformat", $recommend['dateline'] + $timeoffset * 3600);
                    $recommend['lastpost'] = gmdate("$dateformat $timeformat", $recommend['lastpost'] + $timeoffset * 3600);
                }
                $recommendlist[] = $recommend;
            }
            $cache->save($recommendlist,$sql,7200);
        }
        return $recommendlist;
    }
    
    

    
}
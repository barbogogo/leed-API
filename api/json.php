<?php

/*
 @nom: API
 @auteur: Barbogogo
 @description: Page de gestion des flux en json en vue d'une utilisation externe
 */

require_once('../../common.php');
require_once('./constantAPI.php');

define('API_VERSION','0.3');
 
//Récuperation des dossiers de flux par ordre de nom
$folders = $folderManager->populate('name');
//recuperation de tous les flux 
$allFeeds = $feedManager->getFeedsPerFolder();

$user = new User();

$login = $_REQUEST['login'];
$password = $_REQUEST['password'];

$return11 = $user->exist($login, $password);

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

if(PLUGIN_ENABLED == 1)
{
    if($return11 != false)
    {
        switch($_REQUEST['option'])
        {

            case "article":
            
                $target = "*";
            
                $event = $eventManager->loadAllOnlyColumn($target,array('id' => $_REQUEST['idArticle']));
                
                $content = str_replace("%", "%25", $event[0]->getContent());
                
                echo "{\"content\":", json_encode($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), "}\n";
            
                // On met comme lu le event
                $event[0]->change(array('unread'=>'0'),array('id'=>$event[0]->getId()));
                
            break;
            
            case "flux":
                
                $target = "*";
                
                $idFeed = $_REQUEST['feedId'];
                $nbMaxArticle = $_REQUEST['nbMaxArticle'];
                $connectionType = $_REQUEST['connectionType'];
                    $cOnLine  = 0;
                    $cGetData = 1;
                    $cOffLine = 2;
                
                $events = $eventManager->loadAllOnlyColumn($target,array('unread'=>1, 'feed'=>$idFeed),'pubDate DESC', $nbMaxArticle);
                
                $tab = array();
                $iTab = 0;
                
                foreach($events as $event)
                {
                    $tab[$iTab] = array("id" => $event->getId(), 
                                        "title" => html_entity_decode($event->getTitle(), ENT_NOQUOTES, 'UTF-8'), 
                                        "date" => $event->getPubdate("d/m/Y h:i"), 
                                        "urlArticle" => $event->getLink(), 
                                        "author" => $event->getCreator(),
                                        "favorite" => $event->getFavorite(),
                                        "idFeed" => $idFeed);
                    
                    if($connectionType == $cGetData)
                        $tab[$iTab]['content'] = $event->getContent();
                    else
                        $tab[$iTab]['content'] = "null";
                    
                    $iTab ++;
                }
                
                if($iTab == 0)
                {
                    $tab[$iTab] = array("id" => "0", "title" => "Pas d'article pour ce flux");
                }
                
                echo "{\"articles\":", json_encode($tab, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), "}\n";
            break;
            
            case "setRead":
                $target = "*";
                $event = $eventManager->loadAllOnlyColumn($target,array('id' => $_REQUEST['idArticle']));
                // On met comme lu le event
                $event[0]->change(array('unread'=>'0'),array('id'=>$event[0]->getId()));
            break;
            
            case "setUnRead":
                $target = "*";
                $event = $eventManager->loadAllOnlyColumn($target,array('id' => $_REQUEST['idArticle']));
                // On met comme non lu le event
                $event[0]->change(array('unread'=>'1'),array('id'=>$event[0]->getId()));
            break;
            
            case "setFavorite":
                $target = "*";
                $event = $eventManager->loadAllOnlyColumn($target,array('id' => $_REQUEST['idArticle']));
                // On met comme favori
                $event[0]->change(array('favorite'=>'1'),array('id'=>$event[0]->getId()));
            break;
            
            case "unsetFavorite":
                $target = "*";
                $event = $eventManager->loadAllOnlyColumn($target,array('id' => $_REQUEST['idArticle']));
                // On met comme non favori
                $event[0]->change(array('favorite'=>'0'),array('id'=>$event[0]->getId()));
            break;
            
            case "getVersions":
            
                $versions['API']  = API_VERSION;
                $versions['Leed'] = VERSION_NUMBER." (".VERSION_NAME.")";
            
                echo "{\"versions\":", json_encode($versions), "}\n";
            
            break;
            
            case "getFolders":
                $tab = array();
                $iTab = 0;
                
                $nbNoRead = $feedManager->countUnreadEvents();
                
                foreach($folders as $folder)
                {
                    $feeds = $allFeeds['folderMap'][$folder->getId()];
                    
                    foreach($feeds as $title => $value)
                    {
                        
                        foreach($nbNoRead as $title2 => $value2)
                        {
                            if($title == $title2)
                            {
                                $allFeeds['folderMap'][$folder->getId()][$title]['nbNoRead'] = $value2;
                            }
                        }
                    }
                    
                    $feeds2 = $allFeeds['folderMap'][$folder->getId()];
                    
                    $tab[$iTab] = array("id" => $folder->getId(), "titre" => $folder->getName(), "flux" => $feeds2);
                    
                    $iTab ++;
                }

                echo "{\"folders\":", json_encode($tab), "}\n";
            break;
            
            default:
            
                // Error#0: no eror
                echo "{\"error\":{\"id\":\"0\",\"message\":\"no error\"}}\n";
            
            break;
        }
    }
    else
    {
        // Error#2: login failed
        echo "{\"error\":{\"id\":\"2\",\"message\":\"login failed\"}}\n";
    }
}
else
{
    // Error#1: plugin disable
    echo "{\"error\":{\"id\":\"1\",\"message\":\"API disabled\"}}\n";
}
?>
<?php

/*
 @nom: API
 @auteur: Barbogogo
 @description: Page de gestion des flux en json en vue d'une utilisation externe
 */

require_once('../../common.php');
require_once('./constantAPI.php');
require_once('./phpError.php');

define('API_VERSION','0.9');
 
//Récuperation des dossiers de flux par ordre de nom
$folders = $folderManager->populate('name');
//recuperation de tous les flux 
$allFeeds = $feedManager->getFeedsPerFolder();

header('Cache-Control: no-cache, must-revalidate');
header('Expires:'.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
header('Content-type: application/json');

$jsonOutput = "";

if(PLUGIN_ENABLED == 1)
{
    if($myUser != false)
    {
        switch($_REQUEST['option'])
        {

            case "article":
            
                $target = "*";
            
                $event = $eventManager->loadAllOnlyColumn($target,array('id' => $_REQUEST['idArticle']));
                
                $content = str_replace("%", "%25", $event[0]->getContent());
                
                $jsonOutput = "{\"content\":".json_encode($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)."}\n";
            
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
                                        "idFeed" => $event->getFeed());
                    
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
                
                $jsonOutput = "{\"articles\":".json_encode($tab, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)."}\n";
            break;
            
            case "getUnread":
                
                $target = "*";
                
                $nbMaxArticle = $_REQUEST['nbMaxArticle'];
                
                $events = $eventManager->loadAllOnlyColumn($target,array('unread'=>1),'pubDate DESC', $nbMaxArticle);
                
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
                                        "idFeed" => $event->getFeed());
                    
                    $tab[$iTab]['content'] = $event->getContent();
                    
                    $iTab ++;
                }
                
                if($iTab == 0)
                {
                    $tab[$iTab] = array("id" => "0", "title" => "Pas d'article non lus");
                }
                
                $jsonOutput = "{\"articles\":".json_encode($tab, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)."}\n";
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
            
                $jsonOutput = "{\"versions\":".json_encode($versions)."}\n";
            
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
                        $allFeeds['folderMap'][$folder->getId()][$title]['nbNoRead'] = 0;
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

                $jsonOutput = "{\"folders\":".json_encode($tab)."}\n";
            break;
            
            case "setFeedRead":
                $target = "*";
                $event = $eventManager->loadAllOnlyColumn($target,array('feed' => $_REQUEST['idFeed']));
                for($i = 0 ; $i < sizeof($event) ; $i++)
                {
                    $event[$i]->change(array('unread'=>'0'),array('id'=>$event[$i]->getId()));
                }
            break;
            
            case "setAllRead":
                $target = "*";
                $event = $eventManager->loadAll(null);
                for($i = 0 ; $i < sizeof($event) ; $i++)
                {
                    $event[$i]->change(array('unread'=>'0'),array('id'=>$event[$i]->getId()));
                }
            break;
            
            default:
            
                // Error#0: no eror
                $jsonOutput = "{\"error\":{\"id\":\"0\",\"message\":\"no error\"}}\n";
            
            break;
        }
    }
    else
    {
        // Error#2: login failed
        $jsonOutput = "{\"error\":{\"id\":\"2\",\"message\":\"login failed\"}}\n";
    }
}
else
{
    // Error#1: plugin disable
    $jsonOutput = "{\"error\":{\"id\":\"1\",\"message\":\"API disabled\"}}\n";
}

if($isErrorPHP == true)
{
    echo $msgErrorPHP;
}
else
{
    echo $jsonOutput;
}

?>


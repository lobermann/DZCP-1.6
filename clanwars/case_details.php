<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_Clanwars')) {
  $qry = db("SELECT s1.id,s1.datum,s1.clantag,s1.gegner,s1.url,s1.xonx,s1.liga,s1.punkte,s1.gpunkte,s1.maps,s1.serverip,s1.servername,
                    s1.serverpwd,s1.bericht,s1.squad_id,s1.gametype,s1.gcountry,s1.lineup,s1.glineup,s1.matchadmins,s2.icon,s2.name,s2.game
           FROM ".$db['cw']." AS s1
           LEFT JOIN ".$db['squads']." AS s2 ON s1.squad_id = s2.id
           WHERE s1.id = '".intval($_GET['id'])."'");
  $get = _fetch($qry);

  if($chkMe != 1 && $chkMe >= 2 && $get['punkte'] == "0" && $get['gpunkte'] == "0")
  {
    if($get['datum'] > time())
    {
      $qryp = db("SELECT * FROM ".$db['cw_player']."
                  WHERE cwid = '".intval($_GET['id'])."'
                  ORDER BY status");
      while($getp = _fetch($qryp))
      {
        if($getp['status'] == "0") $status = _cw_player_want;
        elseif($getp['status'] == "1") $status = _cw_player_dont_want;
        else $status = _cw_player_dont_know;

        if($getp['member'] == $userid)
        {
          if($getp['status'] == "0") $sely = 'checked="checked"';
          elseif($getp['status'] == "1") $seln = 'checked="checked"';
          elseif($getp['status'] == "2") $selm = 'checked="checked"';
        } else {
          $sely = "";
          $seln = "";
          $selm = "";
        }

        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $show_players .= show($dir."/players_show", array("nick" => autor($getp['member']),
                                                          "class" => $class,
                                                          "status" => $status));
      }

      $cntPlayers = cnt($db['cw_player'], " WHERE cwid = '".intval($_GET['id'])."' AND member = '".$userid."'", "cwid");

      if($cntPlayers) $value = _button_value_edit;
      else            $value = _button_value_add;

      $form_player = "";
	  if(db("SELECT id FROM ".$db['squaduser']." WHERE squad = '".$get['squad_id']."' AND user = '".$userid."'",true)) 
	  		$form_player = show($dir."/form_player",array("id" => intval($_GET['id']),
															 "admin" => (permission('clanwars') ? '<input id="contentSubmitAdmin" type="button" value="'._cw_reset_button.'" class="submit" onclick="DZCP.submitButton(\'contentSubmitAdmin\');DZCP.goTo(\'?action=resetplayers&amp;id='.intval($_GET['id']).'\')" />' : ''),
															 "yes" => _yes,
															 "no" => _no,
															 "sely" => (empty($sely) && empty($seln) && empty($selm) ? 'checked="checked"' : $sely),
															 "seln" => $seln,
															 "selm" => $selm,
															 "maybe" => _maybe,
                                             				 "value" => $value,
                                            				 "play" => _cw_players_play));
	  
      $players = show($dir."/players", array("show_players" => $show_players,
                                             "nick" => _nick,
                                             "status" => _status,
                                             "head" => _cw_players_head,
                                             "form_player" => $form_player));

      $serverpwd = show(_cw_serverpwd, array("cw_serverpwd" => re($get['serverpwd'])));
    } else {
      $serverpwd = "";
    }
  } else {
    $serverpwd = "";
    $players = "";
  }
  $img = squad($get['icon']);
  $show = show(_cw_details_squad, array("game" => re($get['game']),
                                                          "name" => re($get['name']),
                                        "id" => $get['squad_id'],
                                                          "img" => $img));
  $flagge = flag($get['gcountry']);
  $gegner = show(_cw_details_gegner_blank, array("gegner" => re($get['clantag']." - ".$get['gegner']),
                                                 "url" => !empty($get['url']) ? re($get['url']) : "#"));
  $server = show(_cw_details_server, array("servername" => re($get['servername']),
                                           "serverip" => re($get['serverip'])));

  if($get['punkte'] == "0" && $get['gpunkte'] == "0") $result = _cw_no_results;
  else $result = cw_result_details($get['punkte'], $get['gpunkte']);

  if(permission("clanwars"))
  {
    $editcw = show("page/button_edit_single", array("id" => $get['id'],
                                                   "action" => "action=admin&amp;do=edit",
                                                   "title" => _button_title_edit));
  } else {
    $editcw = "";
  }

  if($get['bericht']) $bericht = bbcode($get['bericht']);
  else $bericht = "&nbsp;";

  $libPath = "inc/images/clanwars/".intval($_GET['id']);
  $screen1 = ''; $screen2 = ''; $screen3 = ''; $screen4 = '';
  foreach($picformat AS $end)
  {
    if(file_exists(basePath."/inc/images/clanwars/".intval($_GET['id']).'_1.'.$end)) $screen1 = img_cw($libPath, '1.'.$end);
    if(file_exists(basePath."/inc/images/clanwars/".intval($_GET['id']).'_2.'.$end)) $screen2 = img_cw($libPath, '2.'.$end);
    if(file_exists(basePath."/inc/images/clanwars/".intval($_GET['id']).'_3.'.$end)) $screen3 = img_cw($libPath, '3.'.$end);
    if(file_exists(basePath."/inc/images/clanwars/".intval($_GET['id']).'_4.'.$end)) $screen4 = img_cw($libPath, '4.'.$end);
  }


  if(!empty($screen1) || !empty($screen2) || !empty($screen3) || !empty($screen4))
  {
    $screens = show($dir."/screenshots", array("head" => _cw_screens,
                                               "screenshot1" => _cw_screenshot." 1",
                                               "screenshot2" => _cw_screenshot." 2",
                                               "screenshot3" => _cw_screenshot." 3",
                                               "screenshot4" => _cw_screenshot." 4",
                                               "screen1" => $screen1,
                                               "screen2" => $screen2,
                                               "screen3" => $screen3,
                                               "screen4" => $screen4));
  }

    $qryc = db("SELECT * FROM ".$db['cw_comments']."
                            WHERE cw = ".intval($_GET['id'])."
                            ORDER BY datum DESC
              LIMIT ".($page - 1)*config('m_cwcomments').",".config('m_cwcomments')."");

  $entrys = cnt($db['cw_comments'], " WHERE cw = ".intval($_GET['id']));
  $i = $entrys-($page - 1)*config('m_cwcomments');

    while($getc = _fetch($qryc))
    {
    if($getc['hp']) $hp = show(_hpicon, array("hp" => $getc['hp']));
    else $hp = "";

    if(($chkMe >= 1 && $getc['reg'] == $userid) || permission("clanwars"))
    {
      $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                    "action" => "action=details&amp;do=edit&amp;cid=".$getc['id'],
                                                    "title" => _button_title_edit));
      $delete = show("page/button_delete_single", array("id" => $_GET['id'],
                                                       "action" => "action=details&amp;do=delete&amp;cid=".$getc['id'],
                                                       "title" => _button_title_del,
                                                       "del" => convSpace(_confirm_del_entry)));
    } else {
      $edit = "";
      $delete = "";
    }

        if($getc['reg'] == "0")
        {
      if($getc['hp']) $hp = show(_hpicon_forum, array("hp" => $getc['hp']));
      else $hp = "";
      if($getc['email']) $email = '<br />'.show(_emailicon_forum, array("email" => eMailAddr($getc['email'])));
      else $email = "";
      $onoff = "";
      $avatar = "";
      $nick = show(_link_mailto, array("nick" => re($getc['nick']),
                                       "email" => $getc['email']));
        } else {
      $hp = "";
      $email = "";
      $onoff = onlinecheck($getc['reg']);
      $nick = autor($getc['reg']);

        }

    $titel = show(_eintrag_titel, array("postid" => $i,
                                                                            "datum" => date("d.m.Y", $getc['datum']),
                                                                            "zeit" => date("H:i", $getc['datum'])._uhr,
                                        "edit" => $edit,
                                        "delete" => $delete));

    if($chkMe == "4") $posted_ip = $getc['ip'];
    else $posted_ip = _logged;

        $comments .= show("page/comments_show", array("titel" => $titel,
                                                                                          "comment" => bbcode($getc['comment']),
                                                  "editby" => bbcode($getc['editby']),
                                                  "nick" => $nick,
                                                  "hp" => $hp,
                                                  "email" => $email,
                                                  "avatar" => useravatar($getc['reg']),
                                                  "onoff" => $onoff,
                                                  "rank" => getrank($getc['reg']),
                                                  "ip" => $posted_ip));
      $i--;
    }

  if(settings("reg_cwcomments") && !$chkMe)
  {
    $add = _error_unregistered_nc;
  } else {
    if(!ipcheck("cwid(".$_GET['id'].")", config('f_cwcom')))
    {
      if($userid >= 1)
        {
          $form = show("page/editor_regged", array("nick" => autor($userid),
                                                 "von" => _autor));
        } else {
        $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                    "emailhead" => _email,
                                                    "hphead" => _hp,
                                                    "postemail" => $postemail,
                                                                                              "posthp" => $posthp,
                                                                                              "postnick" => $postnick,));
      }

        $add = show("page/comments_add", array("titel" => _cw_comments_add,
                                                                                     "nickhead" => _nick,
                                                                                     "bbcodehead" => _bbcode,
                                                                                     "emailhead" => _email,
                                                                                     "hphead" => _hp,
                                             "security" => _register_confirm,
                                             "sec" => $dir,
                                             "security" => _register_confirm,
                                             "sec" => $dir,
                                             "show" => "none",
                                             "ip" => _iplog_info,
                                             "preview" => _preview,
                                             "action" => '?action=details&amp;do=add&amp;id='.$_GET['id'],
                                             "prevurl" => '../clanwars/?action=compreview&amp;id='.$_GET['id'],
                                                                                     "id" => $_GET['id'],
                                             "what" => _button_value_add,
                                             "form" => $form,
                                                                                     "posteintrag" => "",
                                                                                     "error" => "",
                                                                                     "eintraghead" => _eintrag));
    } else {
      $add = "";
    }
  }

  $seiten = nav($entrys,config('m_cwcomments'),"?action=details&amp;id=".$_GET['id']."");

  $comments = show($dir."/comments",array("head" => _cw_comments_head,
                                                                               "show" => $comments,
                                          "seiten" => $seiten,
                                          "add" => $add));

  $logo_squad = '_defaultlogo.jpg'; $logo_gegner = '_defaultlogo.jpg';
  foreach($picformat AS $end)
  {
       if(file_exists(basePath.'/inc/images/clanwars/'.$get['id'].'_logo.'.$end)) $logo_gegner = $get['id'].'_logo.'.$end;
    if(file_exists(basePath.'/inc/images/squads/'.$get['squad_id'].'_logo.'.$end))$logo_squad = $get['squad_id'].'_logo.'.$end;
  }

  $logos = ($logo_squad == '_defaultlogo.jpg') && ($logo_gegner == '_defaultlogo.jpg');
  $pagetitle = re($get['name']).' vs. '.re($get['gegner']).' - '.$pagetitle;

  $index = show($dir."/details", array("head" => _cw_head_details,
                                                         "result_head" => _cw_head_results,
                                                         "lineup_head" => _cw_head_lineup,
                                                         "admin_head" => _cw_head_admin,
                                                         "gametype_head" => _cw_head_gametype,
                                                         "squad_head" => _cw_head_squad,
                                                         "flagge" => $flagge,
                                       "br1" => ($logos ? '<!--' : ''),
                                       "br2" => ($logos ? '-->' : ''),
                                       "logo_squad" => $logo_squad,
                                       "logo_gegner" => $logo_gegner,
                                                         "squad" => $show,
                                                         "squad_name" => re($get['name']),
                                                         "gametype" => empty($get['gametype']) ? '-' : re($get['gametype']),
                                                         "lineup" => preg_replace("#\,#","<br />",re($get['lineup'])),
                                                         "glineup" => preg_replace("#\,#","<br />",re($get['glineup'])),
                                                         "match_admins" => empty($get['matchadmins']) ? '-' : re($get['matchadmins']),
                                       "datum" => _datum,
                                       "gegner" => _cw_head_gegner,
                                       "xonx" => _cw_head_xonx,
                                       "liga" => _cw_head_liga,
                                       "maps" => _cw_maps,
                                       "server" => _server,
                                       "result" => _cw_head_result,
                                       "players" => $players,
                                       "edit" => $editcw,
                                       "comments" => $comments,
                                       "bericht" => _cw_bericht,
                                       "serverpwd" => $serverpwd,
                                       "cw_datum" => date("d.m.Y H:i", $get['datum'])._uhr,
                                       "cw_gegner" => $gegner,
                                       "cw_xonx" => empty($get['xonx']) ? '-' : re($get['xonx']),
                                       "cw_liga" => empty($get['liga']) ? '-' : re($get['liga']),
                                       "cw_maps" => empty($get['maps']) ? '-' : re($get['maps']),
                                       "cw_server" => $server,
                                       "cw_result" => $result,
                                       "cw_bericht" => $bericht,
                                       "screenshots" => $screens));

  if($do == "add")
  {
        if(_rows(db("SELECT `id` FROM ".$db['cw']." WHERE `id` = '".(int)$_GET['id']."'")) != 0)
        {
            if(settings("reg_cwcomments") && !$chkMe )
            {
                $index = error(_error_have_to_be_logged, 1);
            } else {
                if(!ipcheck("cwid(".$_GET['id'].")", config('f_cwcom')))
                {
                    if($userid >= 1)
                        $toCheck = empty($_POST['comment']);
                    else
                        $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['comment']) || !check_email($_POST['email']) || $_POST['secure'] != $_SESSION['sec_'.$dir] || empty($_SESSION['sec_'.$dir]);

                    if($toCheck)
                    {
                        if($userid >= 1)
                        {
                            if(empty($_POST['comment'])) $error = _empty_eintrag;
                            $form = show("page/editor_regged", array("nick" => autor($userid),
                                                                                                             "von" => _autor));
                        } else {
                            if(($_POST['secure'] != $_SESSION['sec_'.$dir]) || empty($_SESSION['sec_'.$dir])) $error = _error_invalid_regcode;
                            elseif(empty($_POST['nick'])) $error = _empty_nick;
                            elseif(empty($_POST['email'])) $error = _empty_email;
                            elseif(!check_email($_POST['email'])) $error = _error_invalid_email;
                            elseif(empty($_POST['comment'])) $error = _empty_eintrag;
                            $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                                                                                    "emailhead" => _email,
                                                                                                                    "hphead" => _hp));
                        }

                        $error = show("errors/errortable", array("error" => $error));
                        $index = show("page/comments_add", array("titel" => _cw_comments_add,
                                                                                                         "nickhead" => _nick,
                                                                                                         "bbcodehead" => _bbcode,
                                                                                                         "emailhead" => _email,
                                                                                                         "hphead" => _hp,
                                                                                                         "ip" => _iplog_info,
                                                                                                         "security" => _register_confirm,
                                                                                                         "what" => _button_value_add,
                                                                                                         "sec" => $dir,
                                                                                                         "form" => $form,
                                                                                                         "preview" => _preview,
                                                                                                         "action" => '?action=details&amp;do=add&amp;id='.$_GET['id'],
                                                                                                         "prevurl" => '../clanwars/?action=compreview&id='.$_GET['id'],
                                                                                                         "id" => $_GET['id'],
                                                                                                         "show" => "",
                                                                                                         "postemail" => $_POST['email'],
                                                                                                         "posthp" => links($_POST['hp']),
                                                                                                         "postnick" => re($_POST['nick']),
                                                                                                         "posteintrag" => re_bbcode($_POST['comment']),
                                                                                                         "error" => $error,
                                                                                                         "eintraghead" => _eintrag));
                    } else {
                        $qry = db("INSERT INTO ".$db['cw_comments']."
                                             SET `cw`       = '".((int)$_GET['id'])."',
                                                     `datum`    = '".((int)time())."',
                                                     `nick`     = '".up($_POST['nick'])."',
                                                     `email`    = '".up($_POST['email'])."',
                                                     `hp`       = '".links($_POST['hp'])."',
                                                     `reg`      = '".((int)$userid)."',
                                                     `comment`  = '".up($_POST['comment'],1)."',
                                                     `ip`       = '".$userip."'");

                        setIpcheck("cwid(".$_GET['id'].")");

                        $index = info(_comment_added, "?action=details&amp;id=".$_GET['id']."");
                    }
                } else {
                    $index = error(show(_error_flood_post, array("sek" => config('f_cwcom'))), 1);
                }
            }
        } else{
            $index = error(_id_dont_exist,1);
        }
  }

  if($do == "delete")
  {
    $qry = db("SELECT reg FROM ".$db['cw_comments']."
               WHERE id = '".intval($_GET['cid'])."'");
    $get = _fetch($qry);

      if($get['reg'] == $userid || permission('clanwars'))
      {
      $qry = db("DELETE FROM ".$db['cw_comments']."
                 WHERE id = '".intval($_GET['cid'])."'");

      $index = info(_comment_deleted, "?action=details&amp;id=".intval($_GET['id'])."");
    } else {
      $index = error(_error_wrong_permissions, 1);
    }
  } elseif($do == "editcom") {
    $qry = db("SELECT * FROM ".$db['cw_comments']."
               WHERE id = '".intval($_GET['cid'])."'");
    $get = _fetch($qry);

      if($get['reg'] == $userid || permission('clanwars'))
      {
        $editedby = show(_edited_by, array("autor" => autor($userid),
                                           "time" => date("d.m.Y H:i", time())._uhr));
        $qry = db("UPDATE ".$db['cw_comments']."
                   SET `nick`     = '".up($_POST['nick'])."',
                       `email`    = '".up($_POST['email'])."',
                       `hp`       = '".links($_POST['hp'])."',
                       `comment`  = '".up($_POST['comment'],1)."',
                       `editby`   = '".addslashes($editedby)."'
                   WHERE id = '".intval($_GET['cid'])."'");

        $index = info(_comment_edited, "?action=details&amp;id=".$_GET['id']."");
      } else {
        $index = error(_error_edit_post,1);
      }
    } elseif($do == "edit") {
      $qry = db("SELECT * FROM ".$db['cw_comments']."
                 WHERE id = '".intval($_GET['cid'])."'");
      $get = _fetch($qry);

      if($get['reg'] == $userid || permission('clanwars'))
      {
        if($get['reg'] != 0)
          {
              $form = show("page/editor_regged", array("nick" => autor($get['reg']),
                                                   "von" => _autor));
          } else {
          $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                      "emailhead" => _email,
                                                      "hphead" => _hp,
                                                      "postemail" => $get['email'],
                                                                                              "posthp" => links($get['hp']),
                                                                                                  "postnick" => re($get['nick']),
                                                      ));
        }

            $index = show("page/comments_add", array("titel" => _comments_edit,
                                                                                           "nickhead" => _nick,
                                                                                         "bbcodehead" => _bbcode,
                                                                                         "emailhead" => _email,
                                                                                         "hphead" => _hp,
                                                 "security" => _register_confirm,
                                                 "sec" => $dir,
                                                 "form" => $form,
                                                 "preview" => _preview,
                                                 "prevurl" => '../clanwars/?action=compreview&do=edit&id='.$_GET['id'].'&amp;cid='.$_GET['cid'],
                                                 "action" => '?action=details&amp;do=editcom&amp;id='.$_GET['id'].'&amp;cid='.$_GET['cid'],
                                                 "ip" => _iplog_info,
                                                 "lang" => $language,
                                                                                         "id" => $_GET['id'],
                                                 "what" => _button_value_edit,
                                                 "show" => "",
                                                                                         "posteintrag" => re_bbcode($get['comment']),
                                                                                         "error" => "",
                                                                                         "eintraghead" => _eintrag));
      } else {
        $index = error(_error_edit_post,1);
      }
    }
}
<?php
/**
 * mail
 *
 * @author Peter Gabriel <pgabriel@databay.de>
 * @package ilias
 * @version $Id$
 */
include_once("./include/ilias_header.inc");
include("./include/inc.main.php");

if ($_GET["folder"] == "")
	$folder = "inbox";
else
	$folder = $_GET["folder"];

$tpl = new Template("tpl.mail.html", true, true);

$lng = new Language($ilias->account->data["language"]);

//get mails from user
$myMails = new UserMail($ilias->db, $ilias->account->Id);

$mails = $myMails->getMail();

//mailactions
//possible actions are:
//read
//del
//mark_unread
//mark_read
//move_to_folder
if ($_POST["func"] != "")
{
	switch ($_POST["func"])
	{
		case "read":
			//check if a mail is selected
			if ($marker[0]!="")
			{
				header("location: mail_read.php?id=".$marker[0]);
			}
			break;
		case "del":
			for ($i=0; $i<count($marker); $i++)
			{
				$myMails->delete($marker[$i]);
			}
			header("location: mail.php?folder=".$folder);
			break;
		case "mark_read":
			vd($_POST);
			header("location: mail_read.php?id=");
			break;
		case "mark_unread":
			header("location: mail_read.php?id=");
			break;
	}
}

$tpl->setVariable("TXT_PAGEHEADLINE", $lng->txt("mail"));

$tplbtn = new Template("tpl.buttons.html", true, true);
$tplbtn->setCurrentBlock("btn_cell");
$tplbtn->setVariable("BTN_LINK","./mail.php?folder=inbox");
$tplbtn->setVariable("BTN_TXT", $lng->txt("inbox"));
$tplbtn->parseCurrentBlock();
$tplbtn->setCurrentBlock("btn_cell");
$tplbtn->setVariable("BTN_LINK", "mail_new.php");
$tplbtn->setVariable("BTN_TXT", $lng->txt("compose"));
$tplbtn->parseCurrentBlock();
$tplbtn->setCurrentBlock("btn_cell");
$tplbtn->setVariable("BTN_LINK", "mail_options.php");
$tplbtn->setVariable("BTN_TXT", $lng->txt("options"));
$tplbtn->parseCurrentBlock();

$tplbtn->setCurrentBlock("btn_row");
$tplbtn->parseCurrentBlock();

$tplbtn->setCurrentBlock("btn_cell");
$tplbtn->setVariable("BTN_LINK","");
$tplbtn->setVariable("BTN_TXT", $lng->txt("old"));
$tplbtn->parseCurrentBlock();
$tplbtn->setCurrentBlock("btn_cell");
$tplbtn->setVariable("BTN_LINK","");
$tplbtn->setVariable("BTN_TXT", $lng->txt("sent"));
$tplbtn->parseCurrentBlock();
$tplbtn->setCurrentBlock("btn_cell");
$tplbtn->setVariable("BTN_LINK","");
$tplbtn->setVariable("BTN_TXT", $lng->txt("saved"));
$tplbtn->parseCurrentBlock();
$tplbtn->setCurrentBlock("btn_cell");
$tplbtn->setVariable("BTN_LINK","");
$tplbtn->setVariable("BTN_TXT", $lng->txt("deleted"));
$tplbtn->parseCurrentBlock();

$tplbtn->setCurrentBlock("btn_row");
$tplbtn->parseCurrentBlock();

$tpl->setVariable("BUTTONS",$tplbtn->get());

//set actionsselectbox
$tpl->setVariable("TXT_ACTIONS", $lng->txt("actions"));
$tpl->setCurrentBlock("mailactions");
$tpl->setVariable("MAILACTION_VALUE", "del");
$tpl->setVariable("MAILACTION_OPTION", $lng->txt("delete_selected"));
$tpl->parseCurrentBlock();
$tpl->setCurrentBlock("mailactions");
$tpl->setVariable("MAILACTION_VALUE", "mark_read");
$tpl->setVariable("MAILACTION_OPTION", $lng->txt("mark_all_read"));
$tpl->parseCurrentBlock();
$tpl->setCurrentBlock("mailactions");
$tpl->setVariable("MAILACTION_VALUE", "mark_unread");
$tpl->setVariable("MAILACTION_OPTION", $lng->txt("mark_all_unread"));
$tpl->parseCurrentBlock();

//set movetoselectbox
$tpl->setVariable("TXT_MOVE_TO", $lng->txt("move_to"));
$tpl->setCurrentBlock("mailmove");
$tpl->setVariable("MAILMOVETO_VALUE", "inbox");
$tpl->setVariable("MAILMOVETO_OPTION", $lng->txt("inbox"));
$tpl->parseCurrentBlock();


// output mails
foreach ($mails["msg"] as $row)
{
	$i++;
	$tpl->setCurrentBlock("row");
	$tpl->setVariable("ROWCOL","tblrow".(($i % 2)+1));

	//new mail or read mail?
	if ($row["new"] == true)
		$mailclass = "mailunread";
	else
		$mailclass = "mailread";
		
	$tpl->setVariable("MAILCLASS", $mailclass);
	$tpl->setVariable("MAIL_ID", $row["id"]);
	$tpl->setVariable("MAIL_FROM", $row["from"]);
	$tpl->setVariable("MAIL_SUBJ", $row["subject"]);
	$tpl->setVariable("MAIL_DATE", $row["datetime"]);
	$tpl->setVariable("MAIL_LINK_READ", "mail.php?id=".$row["id"]);
	$tpl->setVariable("MAIL_LINK_DEL", "");
	$tpl->setVariable("TXT_DELETE", $lng->txt("delete"));
	$tpl->setVariable("TXT_ARE_YOU_SURE", $lng->txt("are_you_sure"));
	$tpl->parseCurrentBlock();
}

//headline
//get parameter
$tpl->setVariable("FOLDERNAME", $lng->txt($folder));
$tpl->setVariable("TXT_MAIL", $lng->txt("mail_s"));
$tpl->setVariable("TXT_UNREAD", $lng->txt("unread"));
$tpl->setVariable("TXT_DELETE", $lng->txt("delete"));
$tpl->setVariable("TXT_READ", $lng->txt("read"));
$tpl->setVariable("TXT_SELECT_ALL", $lng->txt("select_all"));

$tpl->setVariable("MAIL_COUNT",count($mails));
$tpl->setVariable("MAIL_COUNT_UNREAD", $mails["unread"]);
$tpl->setVariable("TXT_MAIL_S",$lng->txt("mail_s_unread"));
//columns headlines
$tpl->setVariable("TXT_SENDER", $lng->txt("sender"));
$tpl->setVariable("TXT_SUBJECT", $lng->txt("subject"));
//	$tpl->setVariable("MAIL_SORT_SUBJ","link");
$tpl->setVariable("TXT_DATE",$lng->txt("date"));
//	$tpl->setVariable("MAIL_SORT_DATE","link");

$tplmain->setVariable("PAGECONTENT",$tpl->get());
$tplmain->show();

?>
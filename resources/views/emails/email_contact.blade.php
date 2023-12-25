
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scale=yes"/>
<style type="text/css">
* { -webkit-text-size-adjust: none; }
.btn{background-color:#66bab1!important; color:white;text-decoration:none;padding:10px 20px;border-radius:20px;}
 @media only screen and (max-width: 599px) {
*{ width: 100% !important; }
table[class=deviceWidth],
td[class=deviceWidth] { display: block !important; width: 320px !important; }
table[class=deviceWidth2],
table[class=full],
td[class=full] { display: block !important; width: 100% !important; height: auto !important; }
table[class=hidden],
tr[class=hidden],
td[class=hidden],
img[class=hidden] { display: none !important; }
}
</style>

</head>
<body bgcolor="#f3f3f3" style="margin:0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="20"></td>
    </tr>
  <tr>
    <td align="center" bgcolor="f3f3f3">
      <table bgcolor="#ffffff" width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="deviceWidth">
            <!-- header -->
            <tr>
              <td>
                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" background="#243276" class="full">
                  <tr>
                    <td class="logo" align="left" valign="bottom" style="padding:0 0 0 20px;background-color:#243276;"><a href="#" target="_blank"><img border="0" src="http://capem.fr/visuel/logo-capem-emailing.png" alt="CAPEM" width="241" height="76"></a></td>
                  </tr>
                </table>
              </td>
            </tr>
            <!-- contenu -->
            <tr>
              <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; text-align:center; text-transform:uppercase; line-height: 20px; padding:20px;color:#4D548A;"><h2>Nouveau message Capem</h2></td>
            </tr>
            <tr>
            <td style="font-family:Arial, Helvetica, sans-serif; font-size:14px; text-align:left; line-height: normal; padding:0 20px;">
                  <p>Un message vient d'être enregistré sur le site Capem</p>
                  <ul>
                    <li>Nom : {{ $nom_initial }}</li>
                    <li>Societe : {{ $societe_initial }}</li>
                    <li>Telephone : {{ $telephone_initial }}</li>
                    <li>Email : {{ $email_initial }}</li>
                    {{-- objet --}}
                    @if ($objet_initial == 1)
                    <li>Objet : Demande d'abonnement</li>
                    @endif
                    @if ($objet_initial == 2)
                    <li>Objet : Demande de renseignements</li>
                    @endif
                    @if ($objet_initial == 3)
                    <li>Objet : Autre demande</li>
                    @endif
                    {{-- fin objet --}}
                    <li>Objet : {{ $objet_initial }}</li>
                    <li>Message : {{ $message_initial }}</li>
                  </ul>
              </td>
            </tr>
            <tr>
              <td height="30" style="line-height:30px;"></td>
            </tr>
            <tr>
              <td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align:center; line-height: 20px; padding:10px 0;border-top:1px solid #c9c9c7;">
                CAPEM - 20 Avenue Rapp - 75007 PARIS - 01 43 87 89 00 <br>
                Email : <a href="mailto:contact@capem.fr" style="color:black;">contact@capem.fr</a>
              </td>
            </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
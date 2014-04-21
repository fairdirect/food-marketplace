--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: epelia_emails; Type: TABLE DATA; Schema: public; Owner: epelia
--

DELETE FROM epelia_emails;

INSERT INTO epelia_emails (name, subject, content, vars) VALUES ('ordersent', '#shopname# bestätigt Ihre Bestellung #orderNumber#', 'Hallo #firstname# #lastname#,

#shopname# bestätigt hiermit Ihre Bestellung: #orderNumber#

Die Ware wird jetzt verpackt und in den nächsten Tagen an Sie versendet.


Epelia
Warenhandel Gattinger
Konrad-Becker-Str. 11
35102 Lohra
Deutschland

Geschäftsführer: Micha Gattinger
Inhaltlich Verantwortlicher gemäß §6 MDStV: Micha Gattinger
Datenschutzbeauftragter: Matthias Ansorg
Umsatzsteuer-Ident-Nr.: DE-163752546

Kontakt
E-Mail: mail@epelia.com
Telefon: 01803 465283 ("0180 EINKAUF"; 9 ct/min aus dem Festnetz der Deutschen Telekom; aus Mobilfunknetzen können abweichende Preise gelten)
', 'orderNumber, shopname, firstname, lastname');
INSERT INTO epelia_emails (name, subject, content, vars) VALUES ('register', 'Ihre Registrierung bei epelia', 'Vielen Dank für Ihre Registrierung bei epelia.

Zum Bestätigen Ihrer Registrierung benutzen Sie bitte folgenden Link:

#registerLink#


Epelia
Warenhandel Gattinger
Konrad-Becker-Str. 11
35102 Lohra
Deutschland

Geschäftsführer: Micha Gattinger
Inhaltlich Verantwortlicher gemäß §6 MDStV: Micha Gattinger
Datenschutzbeauftragter: Matthias Ansorg
Umsatzsteuer-Ident-Nr.: DE-163752546

Kontakt
E-Mail: mail@epelia.com
Telefon: 01803 465283 ("0180 EINKAUF"; 9 ct/min aus dem Festnetz der Deutschen Telekom; aus Mobilfunknetzen können abweichende Preise gelten)', 'registerLink');
INSERT INTO epelia_emails (name, subject, content, vars) VALUES ('invoiceShop', 'Ihre Rechnung für #month# #year#', 'Anbei erhalten Sie Ihre Rechnung für #month# #year#.


Epelia
Warenhandel Gattinger
Konrad-Becker-Str. 11
35102 Lohra
Deutschland

Geschäftsführer: Micha Gattinger
Inhaltlich Verantwortlicher gemäß §6 MDStV: Micha Gattinger
Datenschutzbeauftragter: Matthias Ansorg
Umsatzsteuer-Ident-Nr.: DE-163752546

Kontakt
E-Mail: mail@epelia.com
Telefon: 01803 465283 ("0180 EINKAUF"; 9 ct/min aus dem Festnetz der Deutschen Telekom; aus Mobilfunknetzen können abweichende Preise gelten)', 'month, year');
INSERT INTO epelia_emails (name, subject, content, vars) VALUES ('resetPassword', 'Zurücksetzen Ihres Passworts', 'Sie haben das Zurücksetzen Ihres Passworts angefordert.

Bitte klicken Sie dazu auf folgenden Link:

#resetPasswordLink#


Epelia
Warenhandel Gattinger
Konrad-Becker-Str. 11
35102 Lohra
Deutschland

Geschäftsführer: Micha Gattinger
Inhaltlich Verantwortlicher gemäß §6 MDStV: Micha Gattinger
Datenschutzbeauftragter: Matthias Ansorg
Umsatzsteuer-Ident-Nr.: DE-163752546

Kontakt
E-Mail: mail@epelia.com
Telefon: 01803 465283 ("0180 EINKAUF"; 9 ct/min aus dem Festnetz der Deutschen Telekom; aus Mobilfunknetzen können abweichende Preise gelten)', 'resetPasswordLink');
INSERT INTO epelia_emails (name, subject, content, vars) VALUES ('registerShop', 'Ihre Shopregistrierung bei epelia', 'Hallo #salutation# #lastname#,

wir freuen uns, dass Sie an einer Teilnahme interesse haben. Mit Epelia entscheiden Sie sich für eine schnell wachsende Gemeinschaft von Hersteller, Produzenten und Händler für gute Waren mit Herstellergarantie. Um eine gute und faire Zusammenarbeit zu ermöglichen, verzichten wir bewusst auf
Fixbeiträge, Einstellgebühren oder Mindestlaufzeiten, es fällt lediglich eine Verkaufsprovision für verkaufte Waren an. Die Konditionen finden Sie im Anhang
in unseren AGB`s.
Sie können Ihren persönlichen Onlineshop nach belieben gestalten und Ihre Arbeit, ihre Person oder Produktion, gerne mit Bilder untermalt, beschreiben und vorstellen. Dieser Shopbereich ist eigenständig und lässt sich auch hervorragend als Webshop auf Ihrem eigenen Webauftritt nutzen.
Auch können bestehende Siegel (BIO, Herkunft, Güte) hier nach unserer Überprüfung eingestellt werden.
Durch eine spezielle allergene Suche haben Sie hier zudem die Möglichkeit, besonders auf diese Käuferschicht durch geeignete Produkte einzugehen.
Im für Privatkunden abgeschlossenen Großhandelsbereich haben Sie die Möglichkeit größere Mengen zu günstigeren Preisen direkt an Gewerbekunden zu verkaufen.
Wenn Sie an einer Teilnahme interesse haben, klicken Sie bitte hier:

#registerLink#

Mit Aktivierung des Links akzeptieren Sie unsere AGB`s, lesen Sie diese bitte genau durch (diese finden Sie im Anhang an diese Mail.)

Sollten Sie Fragen haben, können Sie uns gerne jederzeit erreichen.

-- 
Mit freundlichen Grüßen

Epelia Marktplatz

-----------------------------------------------
Besuchen Sie: www.epelia.com
-----------------------------------------------

Warenhandel Gattinger
Konrad-Becker-Str. 11
35102 Lohra
Tel.: 06426-6041
Fax: 06426-967338

Die Information in dieser E-Mail ist vertraulich. Sie ist ausschließlich für den Adressaten bestimmt. Jeglicher Zugriff, Veröffentlichung, Vervielfältigung oder Weitergabe dieser eMail durch andere Personen als den Adressaten ist untersagt.

--------------------------------------------------------------------------------------------------------------------------------------------------------------- ', 'salutation, lastname, registerLink');
INSERT INTO epelia_emails (name, subject, content, vars) VALUES ('orderCustomer', 'Epelia bedankt sich für Ihre Bestellung Nr. #orderNumber#', 'Hallo #firstname# #lastname#

Am #orderDate# bestellten Sie den Warenkorb mit folgender Bestellnummer: #orderNumber#

mit folgenden Positionen:

#orderContent#

Versandadresse:
#deliveryAddress#

Rechnungsadresse:
#billingAddress#


#payment#


Mit dieser E-Mail bestätigen wir Ihnen den Eingang Ihrer Bestellung. Diese E-Mail ist noch keine Bestellbestätigung. Diese geht Ihnen extra zu.


Widerrufsbelehrung
------------------

Widerrufsrecht:
Sie können Ihre Vertragserklärung innerhalb von 2 Wochen ohne Angabe von Gründen in Textform (z. B. Brief, Fax, E-Mail) oder - wenn Ihnen die Sache vor Fristablauf überlassen wird - durch Rücksendung der Sache widerrufen. Die Frist beginnt nach Erhalt dieser Belehrung in Textform, jedoch nicht vor Eingang der Ware beim Empfänger (bei der wiederkehrenden Lieferung gleichartiger Waren nicht vor dem Eingang der ersten Teillieferung) und auch nicht vor Erfüllung unserer Informationspflichten gemäß § 312c Abs. 2 BGB in Verbindung mit § 1 Abs. 1, 2 und 4 BGB-InfoV sowie unserer Pflichten gemäß § 312e Abs. 1 Satz 1 BGB in Verbindung mit § 3 BGB-InfoV. Zur Wahrung der Widerrufsfrist genügt die rechtzeitige Absendung des Widerrufs oder der Sache. Der Widerruf ist zu richten an:
Die Firmen- und Kontaktdaten entnehmen Sie bitte dem jeweiligen Shop, bei dem Sie eine Bestellung auslösen.

Widerrufsfolgen:
Im Falle eines wirksamen Widerrufs sind die beiderseits empfangenen Leistungen zurückzugewähren und ggf. gezogene Nutzungen (z. B. Zinsen) herauszugeben. Können Sie uns die empfangene Leistung ganz oder teilweise nicht oder nur in verschlechtertem Zustand zurückgewähren, müssen Sie uns insoweit ggf. Wertersatz leisten. Bei der Überlassung von Sachen gilt dies nicht, wenn die Verschlechterung der Sache ausschließlich auf deren Prüfung - wie sie Ihnen etwa im Ladengeschäft möglich gewesen wäre - zurückzuführen ist. Im Übrigen können Sie die Pflicht zum Wertersatz für eine durch die bestimmungsgemäße Ingebrauchnahme der Sache entstandene Verschlechterung vermeiden, indem Sie die Sache nicht wie Ihr Eigentum in Gebrauch nehmen und alles unterlassen, was deren Wert beeinträchtigt. Paketversandfähige Sachen sind auf unsere Gefahr zurückzusenden. Sie haben die Kosten der Rücksendung zu tragen, wenn die gelieferte Ware der bestellten entspricht und wenn der Preis der zurückzusendenden Sache einen Betrag von 40 Euro nicht übersteigt oder wenn Sie bei einem höheren Preis der Sache zum Zeitpunkt des Widerrufs noch nicht die Gegenleistung oder eine vertraglich vereinbarte Teilzahlung erbracht haben. Anderenfalls ist die Rücksendung für Sie kostenfrei. Nicht paketversandfähige Sachen werden bei Ihnen abgeholt. Verpflichtungen zur Erstattung von Zahlungen müssen innerhalb von 30 Tagen erfüllt werden. Die Frist beginnt für Sie mit der Absendung Ihrer Widerrufserklärung oder der Sache, für uns mit deren Empfang.

Ende der Widerufsbelehrung:
Das Widerrufsrecht besteht nicht bei Fernabsatzverträgen zur Lieferung von Waren, die nach Kundenspezifikation angefertigt werden oder eindeutig auf die persönlichen Bedürfnisse zugeschnitten sind oder die auf Grund ihrer Beschaffenheit nicht für eine Rücksendung geeignet sind oder schnell verderben können oder deren Verfalldatum überschritten würde.
Das Widerrufsrecht besteht nicht bei Fernabsatzverträgen zur Lieferung von Audio- oder Videoaufzeichnungen oder von Software, sofern die gelieferten Datenträger vom Verbraucher entsiegelt worden sind.
Wenn Sie Unternehmer im Sinne des § 14 Bürgerlichen Gesetzbuches (BGB) sind und bei Abschluss des Vertrags in Ausübung Ihrer gewerblichen oder selbständigen Tätigkeit handeln, besteht das Widerrufsrecht nicht.


Epelia
Warenhandel Gattinger
Konrad-Becker-Str. 11
35102 Lohra
Deutschland

Geschäftsführer: Micha Gattinger
Inhaltlich Verantwortlicher gemäß §6 MDStV: Micha Gattinger
Datenschutzbeauftragter: Matthias Ansorg
Umsatzsteuer-Ident-Nr.: DE-163752546

Kontakt
E-Mail: mail@epelia.com
Telefon: 01803 465283 ("0180 EINKAUF"; 9 ct/min aus dem Festnetz der Deutschen Telekom; aus Mobilfunknetzen können abweichende Preise gelten)', 'orderNumber, firstname, lastname, orderDate, orderContent, deliveryAddress, billingAddress, payment, shopContent');
INSERT INTO epelia_emails (name, subject, content, vars) VALUES ('orderShop', 'Neue Bestellung eingegangen (Nr. #orderNumber#)', 'Es ist eine neue Bestellung mit der Bestellnummer #orderNumber# eingegangen.

Folgende Produkte wurden bei Ihnen bestellt:
#orderContent#

Für Details und zur Bearbeitung der Bestellung loggen Sie sich bitte auf http://epelia.com/login/ ein.


Epelia
Warenhandel Gattinger
Konrad-Becker-Str. 11
35102 Lohra
Deutschland

Geschäftsführer: Micha Gattinger
Inhaltlich Verantwortlicher gemäß §6 MDStV: Micha Gattinger
Datenschutzbeauftragter: Matthias Ansorg
Umsatzsteuer-Ident-Nr.: DE-163752546

Kontakt
E-Mail: mail@epelia.com
Telefon: 01803 465283 ("0180 EINKAUF"; 9 ct/min aus dem Festnetz der Deutschen Telekom; aus Mobilfunknetzen können abweichende Preise gelten)', 'orderContent, orderNumber');
INSERT INTO epelia_emails (name, subject, content, vars) VALUES ('paymentCustomer', 'Zahlungseingang zu Ihrer Bestellung #orderNumber#', 'Hallo #firstname# #lastname#,

hiermit möchten wir Sie informieren, dass die Zahlung für die Bestellung: #orderNumber# in der Höhe von #value# EUR auf dem Epelia Treuhandkonto eingegangen ist. Der Produzent wird nun Ihre bestellte Ware auf den Versandweg bringen.


Epelia
Warenhandel Gattinger
Konrad-Becker-Str. 11
35102 Lohra
Deutschland

Geschäftsführer: Micha Gattinger
Inhaltlich Verantwortlicher gemäß §6 MDStV: Micha Gattinger
Datenschutzbeauftragter: Matthias Ansorg
Umsatzsteuer-Ident-Nr.: DE-163752546

Kontakt
E-Mail: mail@epelia.com
Telefon: 01803 465283 ("0180 EINKAUF"; 9 ct/min aus dem Festnetz der Deutschen Telekom; aus Mobilfunknetzen können abweichende Preise gelten)', 'orderNumber, firstname, lastname, amount');


--
-- PostgreSQL database dump complete
--


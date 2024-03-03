/* tslint:disable */
/**
 * datasource
 */
export let userName: string = "Andrew Fuller";
export let userMail: string = "andrewfuller@syncfusion.com";

export let folderData: { [key: string]: Object }[] =
[{
    "ID": 1,
    "PID": null,
    "Name": "Favorites",
    "HasChild": true,
    "Expanded": true,
    "Count": ""
}, {
    "ID": 2,
    "PID": 1,
    "Name": "Inbox",
    "HasChild": false,
    "Expanded": false,
    "Count": 19,
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 3,
    "PID": 1,
    "Name": "Sales Reports",
    "HasChild": false,
    "Expanded": false,
    "Count": 4,
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 4,
    "PID": 1,
    "Name": "Personnel",
    "HasChild": false,
    "Expanded": false,
    "Count": 2,
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 5,
    "PID": 1,
    "Name": "Marketing Reports",
    "HasChild": false,
    "Expanded": false,
    "Count": 6,
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 6,
    "PID": 1,
    "Name": "Sent Items",
    "HasChild": false,
    "Expanded": false,
    "Count": "",
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 7,
    "PID": null,
    "Name": userName,
    "HasChild": true,
    "Expanded": true,
    "Count": ""
}, {
    "ID": 8,
    "PID": 7,
    "Name": "Inbox",
    "HasChild": false,
    "Expanded": false,
    "Count": 19,
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 9,
    "PID": 7,
    "Name": "Clutter",
    "HasChild": false,
    "Expanded": false,
    "Count": 5,
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}, {
    "ID": 10,
    "PID": 7,
    "Name": "Drafts",
    "HasChild": false,
    "Expanded": false,
    "Count": "",
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}, {
    "ID": 11,
    "PID": 7,
    "Name": "Sent Items",
    "HasChild": false,
    "Expanded": false,
    "Count": "",
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 12,
    "PID": 7,
    "Name": "Deleted Items",
    "HasChild": false,
    "Expanded": false,
    "Count": "",
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}, {
    "ID": 13,
    "PID": 7,
    "Name": "Archive",
    "HasChild": false,
    "Expanded": false,
    "Count": "",
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}, {
    "ID": 14,
    "PID": 7,
    "Name": "Junk Mail",
    "HasChild": false,
    "Expanded": false,
    "Count": "",
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}, {
    "ID": 15,
    "PID": 7,
    "Name": "Personnel",
    "HasChild": false,
    "Expanded": false,
    "Count": 2,
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 16,
    "PID": 7,
    "Name": "Sales Reports",
    "HasChild": false,
    "Expanded": false,
    "Count": 4,
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 17,
    "PID": 7,
    "Name": "Marketing Reports",
    "HasChild": false,
    "Expanded": false,
    "Count": 6,
    "Favorite": "Favorite-Composite",
    "FavoriteMessage": "Remove from Favorites"
}, {
    "ID": 18,
    "PID": 7,
    "Name": "My Team",
    "HasChild": true,
    "Expanded": true,
    "Count": ""
}, {
    "ID": 19,
    "PID": 18,
    "Name": "Richelle Mead",
    "HasChild": false,
    "Expanded": false,
    "Count": 9,
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}, {
    "ID": 20,
    "PID": 18,
    "Name": "krystine hobson",
    "HasChild": false,
    "Expanded": false,
    "Count": 11,
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}, {
    "ID": 21,
    "PID": 7,
    "Name": "Trash",
    "HasChild": false,
    "Expanded": false,
    "Count": "",
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}, {
    "ID": 22,
    "PID": 7,
    "Name": "Outbox",
    "HasChild": false,
    "Expanded": false,
    "Count": "",
    "Favorite": "Favorite",
    "FavoriteMessage": "Add to Favorites"
}] ;


export let messageDataSourceNew: { [key: string]: Object }[] = [
  {
    "ContactID": "SF10153",
    "text": "Oleg Oneill",
    "ContactTitle": "Get Together on March",
    "Message": "<p>Hi Gretchen Justice,</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. -, sed ut hoc iudicaremus, non esse in iis partem maximam positam beate aut secus vivendi. Equidem, sed audistine modo de Carneade? </p>\r\n\r\n<p><b>Non quam nostram quidem, inquit Pomponius iocans;</b> <i>Tenent mordicus.</i> Quae qui non vident, nihil umquam magnum ac cognitione dignum amaverunt. Summum enim bonum exposuit vacuitatem doloris; In qua quid est boni praeter summam voluptatem, et eam sempiternam? Ut optime, secundum naturam affectum esse possit. </p>\r\n\r\n<p>Quae cum ita sint, effectum est nihil esse malum, quod turpe non sit. Quamvis enim depravatae non sint, pravae tamen esse possunt. Quid ait Aristoteles reliquique Platonis alumni? Quid ergo attinet gloriose loqui, nisi constanter loquare? </p>\r\n\r\n<p>Summus dolor plures dies manere non potest? Naturales divitias dixit parabiles esse, quod parvo esset natura contenta. Pugnant Stoici cum Peripateticis. Duo Reges: constructio interrete. Expressa vero in iis aetatibus, quae iam confirmatae sunt. <i>Scio enim esse quosdam, qui quavis lingua philosophari possint;</i> Qui autem esse poteris, nisi te amor ipse ceperit? Si qua in iis corrigere voluit, deteriora fecit. </p>\r\n\r\n<p>Thanks,</p><p>Oleg Oneill</p>",
    "Email": "olegoneill@syncfusion.com",
    "CC": [
      "Kerry Best"
    ],
    "CCMail": [
      "kerrybest@syncfusion.com"
    ],
    "BCC": [],
    "BCCMail": [],
    "To": "Gretchen Justice",
    "ToMail": "gretchenjustice@syncfusion.com",
    "Image": "styles/images/images/23.png",
    "Time": "12.1 AM",
    "Date": "24/10/2017",
    "Day": "Friday",
    "Folder": "Archive",
    "ReadStyle": "Read",
    "ReadTitle": "Mark as unread",
    "Flagged": "None",
    "FlagTitle": "Flag this message"
  }
];

export function getContacts(): { [key: string]: Object }[] {
    let contacts1: { [key: string]: Object }[] = [];
    for (let i: number = 0; i < messageDataSourceNew.length; i++) {
        addContacts(messageDataSourceNew[i], 'Email', 'text', contacts1);
    }
    return contacts1;
}

function addContacts(messageData: { [key: string]: Object }, mailId: string, text: string, contacts: { [key: string]: Object }[]): { [key: string]: Object }[] {
    let fieldId: string = 'MailId';
    let contacts1: { [key: string]: Object }[] = [];
    let contactData: { [key: string]: Object } = {};
    if (messageData[mailId]) {
        if (messageData[mailId] instanceof Array) {
            let mailIdList: string[] = messageData[mailId] as string[];
            let contactsList: string[] = messageData[text] as string[];
            for (let j: number = 0; j < mailIdList.length; j++) {
                contactData = {};
                if (!istextExist(contacts, mailIdList[j])) {
                    fieldId = 'MailId';
                    contactData[fieldId] = mailIdList[j];
                    fieldId = 'text';
                    contactData[fieldId] = contactsList[j];
                    contactData['Image'] = messageData['Image'];
                    contacts.push(contactData);
                }
            }
        } else {
            if (!istextExist(contacts, messageData[mailId].toString())) {
                contactData[fieldId] = messageData[mailId];
                mailId = 'text';
                contactData[mailId] = messageData[text];
                contactData['Image'] = messageData['Image'];
                contacts.push(contactData);
            }
        }
    }
    return contacts;
}

function istextExist(contacts: { [key: string]: Object }[], text: string): boolean {
    let key: string = 'MailId';
    for (let i: number = 0; i < contacts.length; i++) {
        if (contacts[i][key]) {
            if (contacts[i][key].toString() === text) {
                return true;
            }
        }
    }
    return false;
}
/* tslint:enable */
import java.io.File;
import java.io.FileNotFoundException;

import java.util.Scanner;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.util.ArrayList;
import java.util.List;
import java.util.Date;
import java.util.Properties;

import java.security.Security;

import javax.activation.DataHandler;
import javax.activation.DataSource;
import javax.activation.FileDataSource;

import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.Session;
import javax.mail.internet.AddressException;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeMessage;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.mail.BodyPart;
import javax.mail.Multipart;

import com.sun.mail.smtp.SMTPTransport;

/*
compilar:
javac -cp ".;activation-1.1.jar;javax.mail-1.6.0.jar" Email.java

executar:
java -cp ".;activation-1.1.jar;javax.mail-1.6.0.jar" Email

testar:
java -cp ".;activation-1.1.jar;javax.mail-1.6.0.jar" Email "mail.helpdesk.tec.br" "465" "teste@helpdesk.tec.br" "Senha@135" "teste@helpdesk.tec.br" "debugging webmail linha comando spike" "Spike_sends_a_letter_to_Princess_Celestia_S5E18.webp" "test/teste@helpdesk.tec.br/Spike_sends_a_letter_to_Princess_Celestia_S5E18.webp" "Princess_Celestia_receives_a_letter_S5E18.webp" "test/teste@helpdesk.tec.br/Princess_Celestia_receives_a_letter_S5E18.webp"
*/

/**
 *
 * @author Lucas Fernando Frighetto
 */
public class Email {

    /**
     * @param args the command line arguments
     * @param args[0] SMTP server
     * @param args[1] SMTP port
     * @param args[2] username: e-mail address of sender (FROM)
     * @param args[3] password
     * @param args[4] e-mail address of receiver (TO)
     * @param args[5] subject
     * @param args[(even > 5)] attachment name of next param
     * @param args[(odd > 5)] attachment filename of previous param
     */
    public static void main(String[] args) {                 
        List<FileAttributes> attachments = new ArrayList<>();        
        for(int i = 6; i < args.length; i = i + 2){  
            attachments.add(new FileAttributes(args[i], args[i + 1]));            
        }                    

        String message_body = "";
        try {                                    
            String message_body_filepatch = "temp/" + args[2] + "/message";
            File message_body_file = new File(message_body_filepatch);
            Scanner fileReader = new Scanner(message_body_file);
            StringBuilder sb = new StringBuilder();  
            while (fileReader.hasNextLine()) {
                sb.append(fileReader.nextLine());                            
            }
            fileReader.close(); 
            message_body = sb.toString();
        } catch (FileNotFoundException ex) {
            Logger.getLogger(Email.class.getName()).log(Level.SEVERE, null, ex);
        }
        try {                 
            MessageSender.Send(args[0], args[1], args[2], args[3], args[4], "", args[5], message_body, attachments);                         
        } catch (MessagingException ex) {
            Logger.getLogger(Email.class.getName()).log(Level.SEVERE, null, ex);
        }         
    }        
}

 class MessageSender {

    private MessageSender() {
    }

    /**
     * Send email using SMTP server.
     *
     * @param username username
     * @param password password
     * @param recipientEmail TO recipient
     * @param ccEmail CC recipient. Can be empty if there is no CC recipient
     * @param title title of the message
     * @param message message to be sent
     * @throws AddressException if the email address parse failed
     * @throws MessagingException if the connection is dead or not in the connected state or if the message is not a MimeMessage
     */
    public static void Send(String host, String port, final String username, final String password, String recipientEmail, String ccEmail, String subject, String text_html, List<FileAttributes> attachments) throws AddressException, MessagingException {
        Security.addProvider(new com.sun.net.ssl.internal.ssl.Provider());
        final String SSL_FACTORY = "javax.net.ssl.SSLSocketFactory";

        // Get a Properties object
        Properties properties = System.getProperties();
        properties.setProperty("mail.smtps.host", host);
        properties.setProperty("mail.smtp.socketFactory.class", SSL_FACTORY);
        properties.setProperty("mail.smtp.socketFactory.fallback", "false");
        properties.setProperty("mail.smtp.port", port);
        properties.setProperty("mail.smtp.socketFactory.port", port);
        properties.setProperty("mail.smtps.auth", "true");       
        properties.put("mail.smtps.quitwait", "false");

        Session session = Session.getInstance(properties, null);

        // -- Create a new message --
        final MimeMessage meow = new MimeMessage(session);

        // -- Set the FROM and TO fields --
        meow.setFrom(new InternetAddress(username));
        meow.setRecipients(Message.RecipientType.TO, InternetAddress.parse(recipientEmail, false));

        if (ccEmail.length() > 0) {
            meow.setRecipients(Message.RecipientType.CC, InternetAddress.parse(ccEmail, false));
        }

        meow.setSubject(subject);        
        meow.setSentDate(new Date());  
                
        Multipart multipart = new MimeMultipart();

        BodyPart messageBodyPart = new MimeBodyPart();
        messageBodyPart.setText(text_html);       
        messageBodyPart.addHeader("Content-Type", "text/html; charset=utf-8"); 
        multipart.addBodyPart(messageBodyPart);

        for(FileAttributes attachment : attachments){
            messageBodyPart = new MimeBodyPart();
            messageBodyPart.setFileName(attachment.filename);
            DataSource source = new FileDataSource(attachment.filepatch);
            messageBodyPart.setDataHandler(new DataHandler(source));        
            multipart.addBodyPart(messageBodyPart);
        }

        meow.setContent(multipart);

        SMTPTransport smtpTransport = (SMTPTransport)session.getTransport("smtps");

        smtpTransport.connect(host, username, password);
        smtpTransport.sendMessage(meow, meow.getAllRecipients());      
        smtpTransport.close();
    }
}

class FileAttributes { 
     
    String filename;
    String filepatch;    

    public FileAttributes(String filename, String filepatch) {        
        this.filename = filename;
        this.filepatch = filepatch;
    }
                
}
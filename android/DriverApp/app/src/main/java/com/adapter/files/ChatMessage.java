package com.adapter.files;

import java.util.Date;

/**
 * Created by Hathibelagal on 7/10/16.
 */
public class ChatMessage {

    private String messageText;
    private String messageUser;
    // private long messageTime;
    private String messageId;

    public ChatMessage(String messageText, String messageUser, String messageId ) {
        this.messageText = messageText;
        this.messageUser = messageUser;
        this.messageId = messageId;
        // messageTime = new Date().getTime();
    }

    public ChatMessage(){

    }

    public String getMessageText() {
        return messageText;
    }

    public void setMessageText(String messageText) {
        this.messageText = messageText;
    }

    public String getMessageUser() {
        return messageUser;
    }

    public void setMessageUser(String messageUser) {
        this.messageUser = messageUser;
    }

    // public long getMessageTime() {
        // return messageTime;
    // }

    // public void setMessageTime(long messageTime) {
        // this.messageTime = messageTime;
    // }

    public String getMessageId() {
        return messageId;
    }

    public void setMessageId(String messageId) {
        this.messageId = messageId;
    }
}

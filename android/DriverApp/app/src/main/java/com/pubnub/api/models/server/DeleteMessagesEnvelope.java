package com.pubnub.api.models.server;

import com.google.gson.annotations.SerializedName;

public class DeleteMessagesEnvelope {

    private Integer status;
    private boolean error;
    @SerializedName("error_message")
    private String errorMessage;


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Integer getStatus() {
        return this.status;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isError() {
        return this.error;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getErrorMessage() {
        return this.errorMessage;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setStatus(final Integer status) {
        this.status = status;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setError(final boolean error) {
        this.error = error;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setErrorMessage(final String errorMessage) {
        this.errorMessage = errorMessage;
    }
}

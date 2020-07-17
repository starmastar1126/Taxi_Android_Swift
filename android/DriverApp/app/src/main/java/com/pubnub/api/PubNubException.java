package com.pubnub.api;

import com.google.gson.JsonElement;

import lombok.AccessLevel;
import lombok.Builder;
import lombok.Getter;
import retrofit2.Call;

@Builder
@Getter
public class PubNubException extends Exception {
    private String errormsg = "";
    private PubNubError pubnubError;
    private JsonElement jso;
    private String response;
    private int statusCode;

    @Getter(AccessLevel.NONE)
    private Call affectedCall;

    //    @java.beans.ConstructorProperties({"errormsg", "pubnubError", "jso", "response", "statusCode", "affectedCall"})
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PubNubException(final String errormsg, final PubNubError pubnubError, final JsonElement jso, final String response, final int statusCode, final Call affectedCall) {
        this.errormsg = errormsg;
        this.pubnubError = pubnubError;
        this.jso = jso;
        this.response = response;
        this.statusCode = statusCode;
        this.affectedCall = affectedCall;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PubNubExceptionBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String errormsg;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private PubNubError pubnubError;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private JsonElement jso;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String response;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private int statusCode;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Call affectedCall;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PubNubExceptionBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubExceptionBuilder errormsg(final String errormsg) {
            this.errormsg = errormsg;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubExceptionBuilder pubnubError(final PubNubError pubnubError) {
            this.pubnubError = pubnubError;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubExceptionBuilder jso(final JsonElement jso) {
            this.jso = jso;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubExceptionBuilder response(final String response) {
            this.response = response;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubExceptionBuilder statusCode(final int statusCode) {
            this.statusCode = statusCode;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubExceptionBuilder affectedCall(final Call affectedCall) {
            this.affectedCall = affectedCall;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubException build() {
            return new PubNubException(errormsg, pubnubError, jso, response, statusCode, affectedCall);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PubNubException.PubNubExceptionBuilder(errormsg=" + this.errormsg + ", pubnubError=" + this.pubnubError + ", jso=" + this.jso + ", response=" + this.response + ", statusCode=" + this.statusCode + ", affectedCall=" + this.affectedCall + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PubNubExceptionBuilder builder() {
        return new PubNubExceptionBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getErrormsg() {
        return this.errormsg;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PubNubError getPubnubError() {
        return this.pubnubError;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getJso() {
        return this.jso;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getResponse() {
        return this.response;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getStatusCode() {
        return this.statusCode;
    }
}

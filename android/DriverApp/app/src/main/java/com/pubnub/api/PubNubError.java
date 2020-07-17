package com.pubnub.api;


import com.google.gson.JsonElement;

/**
 * PubNubError object is passed to errorCallback. It contains details of error,
 * like error code, error string, and optional message
 *
 * @author PubNub
 */

public class PubNubError {

    private int errorCode;
    private int errorCodeExtended;
    private JsonElement errorObject;
    /**
     * includes a message from the thrown exception (if any.)
     */
    private String message;
    /**
     * PubNub supplied explanation of the error.
     */
    private String errorString;

    PubNubError(final int errorCode, final int errorCodeExtended, final JsonElement errorObject, final String message, final String errorString) {
        this.errorCode = errorCode;
        this.errorCodeExtended = errorCodeExtended;
        this.errorObject = errorObject;
        this.message = message;
        this.errorString = errorString;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PubNubErrorBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private int errorCode;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private int errorCodeExtended;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private JsonElement errorObject;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String message;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String errorString;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PubNubErrorBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubErrorBuilder errorCode(final int errorCode) {
            this.errorCode = errorCode;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubErrorBuilder errorCodeExtended(final int errorCodeExtended) {
            this.errorCodeExtended = errorCodeExtended;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubErrorBuilder errorObject(final JsonElement errorObject) {
            this.errorObject = errorObject;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubErrorBuilder message(final String message) {
            this.message = message;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubErrorBuilder errorString(final String errorString) {
            this.errorString = errorString;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PubNubError build() {
            return new PubNubError(errorCode, errorCodeExtended, errorObject, message, errorString);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PubNubError.PubNubErrorBuilder(errorCode=" + this.errorCode + ", errorCodeExtended=" + this.errorCodeExtended + ", errorObject=" + this.errorObject + ", message=" + this.message + ", errorString=" + this.errorString + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PubNubErrorBuilder builder() {
        return new PubNubErrorBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getErrorCode() {
        return this.errorCode;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getErrorCodeExtended() {
        return this.errorCodeExtended;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getErrorObject() {
        return this.errorObject;
    }

    /**
     * includes a message from the thrown exception (if any.)
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getMessage() {
        return this.message;
    }

    /**
     * PubNub supplied explanation of the error.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getErrorString() {
        return this.errorString;
    }
}


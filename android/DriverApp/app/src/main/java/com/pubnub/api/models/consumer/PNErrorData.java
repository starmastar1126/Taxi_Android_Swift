package com.pubnub.api.models.consumer;

public class PNErrorData {

    private String information;
    private Exception throwable;

    public PNErrorData(final String information, final Exception throwable) {
        this.information = information;
        this.throwable = throwable;
    }

    public String getInformation() {
        return this.information;
    }

    public Exception getThrowable() {
        return this.throwable;
    }

    @java.lang.Override
    public java.lang.String toString() {
        return "PNErrorData(information=" + this.getInformation() + ", throwable=" + this.getThrowable() + ")";
    }
}

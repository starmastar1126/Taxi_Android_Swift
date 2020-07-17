package com.pubnub.api.models.server;

import com.google.gson.JsonElement;

public class Envelope<T> {
    private int status;
    private String message;
    private String service;
    private T payload;
    private int occupancy;
    private JsonElement uuids;
    private String action;
    private boolean error;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getStatus() {
        return this.status;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getMessage() {
        return this.message;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getService() {
        return this.service;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public T getPayload() {
        return this.payload;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getOccupancy() {
        return this.occupancy;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getUuids() {
        return this.uuids;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getAction() {
        return this.action;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isError() {
        return this.error;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setStatus(final int status) {
        this.status = status;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setMessage(final String message) {
        this.message = message;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setService(final String service) {
        this.service = service;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setPayload(final T payload) {
        this.payload = payload;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setOccupancy(final int occupancy) {
        this.occupancy = occupancy;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setUuids(final JsonElement uuids) {
        this.uuids = uuids;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setAction(final String action) {
        this.action = action;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setError(final boolean error) {
        this.error = error;
    }
}

package com.pubnub.api.models;

import lombok.experimental.Accessors;

@Accessors(chain = true)
public class SubscriptionItem {

    private String name;
    private Object state;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public SubscriptionItem setName(final String name) {
        this.name = name;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public SubscriptionItem setState(final Object state) {
        this.state = state;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getName() {
        return this.name;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Object getState() {
        return this.state;
    }
}

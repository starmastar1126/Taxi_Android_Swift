package com.pubnub.api.builder;


import com.pubnub.api.managers.SubscriptionManager;

import java.util.ArrayList;
import java.util.List;

public abstract class PubSubBuilder {


    private List<String> channelSubscriptions;

    private List<String> channelGroupSubscriptions;

    private SubscriptionManager subscriptionManager;

    public PubSubBuilder(SubscriptionManager subscriptionManagerInstance) {
        this.subscriptionManager = subscriptionManagerInstance;
        this.channelSubscriptions = new ArrayList<>();
        this.channelGroupSubscriptions = new ArrayList<>();
    }


    public PubSubBuilder channels(List<String> channel) {
        channelSubscriptions.addAll(channel);
        return this;
    }

    public PubSubBuilder channelGroups(List<String> channelGroup) {
        channelGroupSubscriptions.addAll(channelGroup);
        return this;
    }

    public abstract void execute();


    protected List<String> getChannelSubscriptions() {
        return this.channelSubscriptions;
    }

    protected void setChannelSubscriptions(final List<String> channelSubscriptions) {
        this.channelSubscriptions = channelSubscriptions;
    }

    protected List<String> getChannelGroupSubscriptions() {
        return this.channelGroupSubscriptions;
    }

    protected void setChannelGroupSubscriptions(final List<String> channelGroupSubscriptions) {
        this.channelGroupSubscriptions = channelGroupSubscriptions;
    }

    protected SubscriptionManager getSubscriptionManager() {
        return this.subscriptionManager;
    }

    protected void setSubscriptionManager(final SubscriptionManager subscriptionManager) {
        this.subscriptionManager = subscriptionManager;
    }
}

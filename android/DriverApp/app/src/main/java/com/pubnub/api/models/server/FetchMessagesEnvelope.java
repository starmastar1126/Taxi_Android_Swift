package com.pubnub.api.models.server;

import java.util.List;
import java.util.Map;

public class FetchMessagesEnvelope {

    private Map<String, List<HistoryForChannelsItem>> channels;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, List<HistoryForChannelsItem>> getChannels() {
        return this.channels;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setChannels(final Map<String, List<HistoryForChannelsItem>> channels) {
        this.channels = channels;
    }
}

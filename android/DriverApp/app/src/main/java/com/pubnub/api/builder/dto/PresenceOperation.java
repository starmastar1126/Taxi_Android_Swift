package com.pubnub.api.builder.dto;

import java.util.List;


public class PresenceOperation {

    private List<String> channels;
    private List<String> channelGroups;
    private boolean connected;


    PresenceOperation(final List<String> channels, final List<String> channelGroups, final boolean connected) {
        this.channels = channels;
        this.channelGroups = channelGroups;
        this.connected = connected;
    }


    public static class PresenceOperationBuilder {

        private List<String> channels;

        private List<String> channelGroups;

        private boolean connected;


        PresenceOperationBuilder() {
        }


        public PresenceOperationBuilder channels(final List<String> channels) {
            this.channels = channels;
            return this;
        }


        public PresenceOperationBuilder channelGroups(final List<String> channelGroups) {
            this.channelGroups = channelGroups;
            return this;
        }


        public PresenceOperationBuilder connected(final boolean connected) {
            this.connected = connected;
            return this;
        }


        public PresenceOperation build() {
            return new PresenceOperation(channels, channelGroups, connected);
        }

        public java.lang.String toString() {
            return "PresenceOperation.PresenceOperationBuilder(channels=" + this.channels + ", channelGroups=" + this.channelGroups + ", connected=" + this.connected + ")";
        }
    }


    public static PresenceOperationBuilder builder() {
        return new PresenceOperationBuilder();
    }


    public List<String> getChannels() {
        return this.channels;
    }


    public List<String> getChannelGroups() {
        return this.channelGroups;
    }


    public boolean isConnected() {
        return this.connected;
    }
}

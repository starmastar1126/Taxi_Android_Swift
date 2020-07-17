package com.pubnub.api.builder.dto;

import java.util.List;

public class StateOperation {

    private List<String> channels;
    private List<String> channelGroups;
    private Object state;

    StateOperation(final List<String> channels, final List<String> channelGroups, final Object state) {
        this.channels = channels;
        this.channelGroups = channelGroups;
        this.state = state;
    }

    public static class StateOperationBuilder {
        private List<String> channels;
        private List<String> channelGroups;
        private Object state;

        StateOperationBuilder() {
        }

        public StateOperationBuilder channels(final List<String> channels) {
            this.channels = channels;
            return this;
        }

        public StateOperationBuilder channelGroups(final List<String> channelGroups) {
            this.channelGroups = channelGroups;
            return this;
        }

        public StateOperationBuilder state(final Object state) {
            this.state = state;
            return this;
        }

        public StateOperation build() {
            return new StateOperation(channels, channelGroups, state);
        }

        public java.lang.String toString() {
            return "StateOperation.StateOperationBuilder(channels=" + this.channels + ", channelGroups=" + this.channelGroups + ", state=" + this.state + ")";
        }
    }


    public static StateOperationBuilder builder() {
        return new StateOperationBuilder();
    }


    public List<String> getChannels() {
        return this.channels;
    }


    public List<String> getChannelGroups() {
        return this.channelGroups;
    }


    public Object getState() {
        return this.state;
    }
}

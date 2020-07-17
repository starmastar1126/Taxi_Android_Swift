package com.pubnub.api.models.consumer.channel_group;

import java.util.List;

public class PNChannelGroupsListAllResult {
    private List<String> groups;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNChannelGroupsListAllResult(final List<String> groups) {
        this.groups = groups;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNChannelGroupsListAllResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> groups;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNChannelGroupsListAllResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNChannelGroupsListAllResultBuilder groups(final List<String> groups) {
            this.groups = groups;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNChannelGroupsListAllResult build() {
            return new PNChannelGroupsListAllResult(groups);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNChannelGroupsListAllResult.PNChannelGroupsListAllResultBuilder(groups=" + this.groups + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNChannelGroupsListAllResultBuilder builder() {
        return new PNChannelGroupsListAllResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getGroups() {
        return this.groups;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNChannelGroupsListAllResult(groups=" + this.getGroups() + ")";
    }
}

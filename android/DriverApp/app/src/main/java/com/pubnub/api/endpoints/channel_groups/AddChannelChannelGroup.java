package com.pubnub.api.endpoints.channel_groups;

import com.pubnub.api.PubNub;
import com.pubnub.api.PubNubException;
import com.pubnub.api.PubNubUtil;
import com.pubnub.api.builder.PubNubErrorBuilder;
import com.pubnub.api.endpoints.Endpoint;
import com.pubnub.api.enums.PNOperationType;
import com.pubnub.api.managers.RetrofitManager;
import com.pubnub.api.managers.TelemetryManager;
import com.pubnub.api.models.consumer.channel_group.PNChannelGroupsAddChannelResult;
import com.pubnub.api.models.server.Envelope;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import lombok.Setter;
import lombok.experimental.Accessors;
import retrofit2.Call;
import retrofit2.Response;

@Accessors(chain = true, fluent = true)
public class AddChannelChannelGroup extends Endpoint<Envelope, PNChannelGroupsAddChannelResult> {

    private String channelGroup;

    private List<String> channels;


    public AddChannelChannelGroup(PubNub pubnub, TelemetryManager telemetryManager, RetrofitManager retrofit) {
        super(pubnub, telemetryManager, retrofit);
        channels = new ArrayList<>();
    }

    @Override
    protected List<String> getAffectedChannels() {
        return channels;
    }

    @Override
    protected List<String> getAffectedChannelGroups() {
        return Collections.singletonList(channelGroup);
    }

    @Override
    protected void validateParams() throws PubNubException {
        if (channelGroup == null || channelGroup.isEmpty()) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_GROUP_MISSING).build();
        }
        if (channels.size() == 0) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_CHANNEL_MISSING).build();
        }
    }

    @Override
    protected Call<Envelope> doWork(Map<String, String> params) {
        if (channels.size() > 0) {
            params.put("add", PubNubUtil.joinString(channels, ","));
        }

        return this.getRetrofit().getChannelGroupService().addChannelChannelGroup(this.getPubnub().getConfiguration().getSubscribeKey(), channelGroup, params);
    }

    @Override
    protected PNChannelGroupsAddChannelResult createResponse(Response<Envelope> input) throws PubNubException {
        if (input.body() == null) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_PARSING_ERROR).build();
        }

        return PNChannelGroupsAddChannelResult.builder().build();
    }

    @Override
    protected PNOperationType getOperationType() {
        return PNOperationType.PNAddChannelsToGroupOperation;
    }

    @Override
    protected boolean isAuthRequired() {
        return true;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public AddChannelChannelGroup channelGroup(final String channelGroup) {
        this.channelGroup = channelGroup;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public AddChannelChannelGroup channels(final List<String> channels) {
        this.channels = channels;
        return this;
    }
}

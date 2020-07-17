package com.pubnub.api.endpoints.push;

import com.pubnub.api.PubNub;
import com.pubnub.api.PubNubException;
import com.pubnub.api.PubNubUtil;
import com.pubnub.api.builder.PubNubErrorBuilder;
import com.pubnub.api.endpoints.Endpoint;
import com.pubnub.api.enums.PNOperationType;
import com.pubnub.api.enums.PNPushType;
import com.pubnub.api.managers.RetrofitManager;
import com.pubnub.api.managers.TelemetryManager;
import com.pubnub.api.models.consumer.push.PNPushRemoveChannelResult;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import lombok.Setter;
import lombok.experimental.Accessors;
import retrofit2.Call;
import retrofit2.Response;

@Accessors(chain = true, fluent = true)
public class RemoveChannelsFromPush extends Endpoint<List<Object>, PNPushRemoveChannelResult> {


    private PNPushType pushType;

    private List<String> channels;

    private String deviceId;

    public RemoveChannelsFromPush(PubNub pubnub, TelemetryManager telemetryManager, RetrofitManager retrofit) {
        super(pubnub, telemetryManager, retrofit);

        channels = new ArrayList<>();
    }

    @Override
    protected List<String> getAffectedChannels() {
        return channels;
    }

    @Override
    protected List<String> getAffectedChannelGroups() {
        return null;
    }

    @Override
    protected void validateParams() throws PubNubException {
        if (this.getPubnub().getConfiguration().getSubscribeKey() == null || this.getPubnub().getConfiguration().getSubscribeKey().isEmpty()) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_SUBSCRIBE_KEY_MISSING).build();
        }
        if (pushType == null) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_PUSH_TYPE_MISSING).build();
        }
        if (deviceId == null || deviceId.isEmpty()) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_DEVICE_ID_MISSING).build();
        }
        if (channels.size() == 0) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_CHANNEL_MISSING).build();
        }
    }

    @Override
    protected Call<List<Object>> doWork(Map<String, String> baseParams) throws PubNubException {
        baseParams.put("type", pushType.name().toLowerCase());

        if (channels.size() != 0) {
            baseParams.put("remove", PubNubUtil.joinString(channels, ","));
        }

        return this.getRetrofit().getPushService().modifyChannelsForDevice(this.getPubnub().getConfiguration().getSubscribeKey(), deviceId, baseParams);

    }

    @Override
    protected PNPushRemoveChannelResult createResponse(Response<List<Object>> input) throws PubNubException {
        if (input.body() == null) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_PARSING_ERROR).build();
        }

        return PNPushRemoveChannelResult.builder().build();
    }

    @Override
    protected PNOperationType getOperationType() {
        return PNOperationType.PNRemovePushNotificationsFromChannelsOperation;
    }

    @Override
    protected boolean isAuthRequired() {
        return true;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public RemoveChannelsFromPush pushType(final PNPushType pushType) {
        this.pushType = pushType;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public RemoveChannelsFromPush channels(final List<String> channels) {
        this.channels = channels;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public RemoveChannelsFromPush deviceId(final String deviceId) {
        this.deviceId = deviceId;
        return this;
    }
}

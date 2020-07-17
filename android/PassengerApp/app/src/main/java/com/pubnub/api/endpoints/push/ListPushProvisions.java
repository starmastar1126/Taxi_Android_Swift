package com.pubnub.api.endpoints.push;

import com.pubnub.api.PubNub;
import com.pubnub.api.PubNubException;
import com.pubnub.api.builder.PubNubErrorBuilder;
import com.pubnub.api.endpoints.Endpoint;
import com.pubnub.api.enums.PNOperationType;
import com.pubnub.api.enums.PNPushType;
import com.pubnub.api.managers.RetrofitManager;
import com.pubnub.api.managers.TelemetryManager;
import com.pubnub.api.models.consumer.push.PNPushListProvisionsResult;

import java.util.List;
import java.util.Map;

import lombok.experimental.Accessors;
import retrofit2.Call;
import retrofit2.Response;

@Accessors(chain = true, fluent = true)
public class ListPushProvisions extends Endpoint<List<String>, PNPushListProvisionsResult> {

    private PNPushType pushType;
    private String deviceId;

    public ListPushProvisions(PubNub pubnub, TelemetryManager telemetryManager, RetrofitManager retrofit) {
        super(pubnub, telemetryManager, retrofit);
    }

    @Override
    protected List<String> getAffectedChannels() {
        return null;
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
    }

    @Override
    protected Call<List<String>> doWork(Map<String, String> params) throws PubNubException {
        params.put("type", pushType.name().toLowerCase());
        return this.getRetrofit().getPushService().listChannelsForDevice(this.getPubnub().getConfiguration().getSubscribeKey(), deviceId, params);
    }

    @Override
    protected PNPushListProvisionsResult createResponse(Response<List<String>> input) throws PubNubException {
        if (input.body() == null) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_PARSING_ERROR).build();
        }

        return PNPushListProvisionsResult.builder().channels(input.body()).build();
    }

    @Override
    protected PNOperationType getOperationType() {
        return PNOperationType.PNPushNotificationEnabledChannelsOperation;
    }

    @Override
    protected boolean isAuthRequired() {
        return true;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public ListPushProvisions pushType(final PNPushType pushType) {
        this.pushType = pushType;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public ListPushProvisions deviceId(final String deviceId) {
        this.deviceId = deviceId;
        return this;
    }
}

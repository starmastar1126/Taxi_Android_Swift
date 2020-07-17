package com.pubnub.api.endpoints.presence;

import com.google.gson.JsonElement;
import com.pubnub.api.PubNub;
import com.pubnub.api.PubNubException;
import com.pubnub.api.PubNubUtil;
import com.pubnub.api.builder.PubNubErrorBuilder;
import com.pubnub.api.endpoints.Endpoint;
import com.pubnub.api.enums.PNOperationType;
import com.pubnub.api.managers.MapperManager;
import com.pubnub.api.managers.RetrofitManager;
import com.pubnub.api.managers.TelemetryManager;
import com.pubnub.api.models.consumer.presence.PNGetStateResult;
import com.pubnub.api.models.server.Envelope;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import lombok.Setter;
import lombok.experimental.Accessors;
import retrofit2.Call;
import retrofit2.Response;

@Accessors(chain = true, fluent = true)
public class GetState extends Endpoint<Envelope<JsonElement>, PNGetStateResult> {


    private List<String> channels;

    private List<String> channelGroups;

    private String uuid;

    public GetState(PubNub pubnub, TelemetryManager telemetryManager, RetrofitManager retrofit) {
        super(pubnub, telemetryManager, retrofit);
        channels = new ArrayList<>();
        channelGroups = new ArrayList<>();
    }

    @Override
    protected List<String> getAffectedChannels() {
        return channels;
    }

    @Override
    protected List<String> getAffectedChannelGroups() {
        return channelGroups;
    }

    @Override
    protected void validateParams() throws PubNubException {
        if (this.getPubnub().getConfiguration().getSubscribeKey() == null || this.getPubnub().getConfiguration().getSubscribeKey().isEmpty()) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_SUBSCRIBE_KEY_MISSING).build();
        }
        if (channels.size() == 0 && channelGroups.size() == 0) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_CHANNEL_AND_GROUP_MISSING).build();
        }
    }

    @Override
    protected Call<Envelope<JsonElement>> doWork(Map<String, String> params) {
        if (channelGroups.size() > 0) {
            params.put("channel-group", PubNubUtil.joinString(channelGroups, ","));
        }

        String channelCSV = channels.size() > 0 ? PubNubUtil.joinString(channels, ",") : ",";

        String selectedUUID = uuid != null ? uuid : this.getPubnub().getConfiguration().getUuid();

        return this.getRetrofit().getPresenceService().getState(this.getPubnub().getConfiguration().getSubscribeKey(), channelCSV, selectedUUID, params);
    }

    @Override
    protected PNGetStateResult createResponse(Response<Envelope<JsonElement>> input) throws PubNubException {
        Map<String, JsonElement> stateMappings = new HashMap<>();
        MapperManager mapper = getPubnub().getMapper();

        if (channels.size() == 1 && channelGroups.size() == 0) {
            stateMappings.put(channels.get(0), input.body().getPayload());
        } else {
            for (Iterator<Map.Entry<String, JsonElement>> it = mapper.getObjectIterator(input.body().getPayload()); it.hasNext(); ) {
                Map.Entry<String, JsonElement> stateMapping = it.next();
                stateMappings.put(stateMapping.getKey(), stateMapping.getValue());
            }
        }

        return PNGetStateResult.builder().stateByUUID(stateMappings).build();
    }

    @Override
    protected PNOperationType getOperationType() {
        return PNOperationType.PNGetState;
    }

    @Override
    protected boolean isAuthRequired() {
        return true;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public GetState channels(final List<String> channels) {
        this.channels = channels;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public GetState channelGroups(final List<String> channelGroups) {
        this.channelGroups = channelGroups;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public GetState uuid(final String uuid) {
        this.uuid = uuid;
        return this;
    }
}

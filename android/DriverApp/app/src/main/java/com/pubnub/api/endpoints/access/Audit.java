package com.pubnub.api.endpoints.access;

import com.pubnub.api.PubNub;
import com.pubnub.api.PubNubException;
import com.pubnub.api.PubNubUtil;
import com.pubnub.api.builder.PubNubErrorBuilder;
import com.pubnub.api.endpoints.Endpoint;
import com.pubnub.api.enums.PNOperationType;
import com.pubnub.api.managers.RetrofitManager;
import com.pubnub.api.managers.TelemetryManager;
import com.pubnub.api.models.consumer.access_manager.PNAccessManagerAuditResult;
import com.pubnub.api.models.server.Envelope;
import com.pubnub.api.models.server.access_manager.AccessManagerAuditPayload;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import lombok.Setter;
import lombok.experimental.Accessors;
import retrofit2.Call;
import retrofit2.Response;

@Accessors(chain = true, fluent = true)
public class Audit extends Endpoint<Envelope<AccessManagerAuditPayload>, PNAccessManagerAuditResult> {

    private List<String> authKeys;
    private String channel;
    private String channelGroup;

    public Audit(PubNub pubnub, TelemetryManager telemetryManager, RetrofitManager retrofit) {
        super(pubnub, telemetryManager, retrofit);
        authKeys = new ArrayList<>();
    }

    @Override
    protected List<String> getAffectedChannels() {
        return Collections.singletonList(channel);
    }

    @Override
    protected List<String> getAffectedChannelGroups() {
        return Collections.singletonList(channelGroup);
    }

    @Override
    protected void validateParams() throws PubNubException {
        if (authKeys.size() == 0) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_AUTH_KEYS_MISSING).build();
        }
        if (this.getPubnub().getConfiguration().getSecretKey() == null || this.getPubnub().getConfiguration().getSecretKey().isEmpty()) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_SECRET_KEY_MISSING).build();
        }
        if (this.getPubnub().getConfiguration().getSubscribeKey() == null || this.getPubnub().getConfiguration().getSubscribeKey().isEmpty()) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_SUBSCRIBE_KEY_MISSING).build();
        }
        if (this.getPubnub().getConfiguration().getPublishKey() == null || this.getPubnub().getConfiguration().getPublishKey().isEmpty()) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_PUBLISH_KEY_MISSING).build();
        }
        if (channel == null && channelGroup == null) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_CHANNEL_AND_GROUP_MISSING).build();
        }
    }

    @Override
    protected Call<Envelope<AccessManagerAuditPayload>> doWork(Map<String, String> queryParams) throws PubNubException {

        if (channel != null) {
            queryParams.put("channel", channel);
        }

        if (channelGroup != null) {
            queryParams.put("channel-group", channelGroup);
        }

        if (authKeys.size() > 0) {
            queryParams.put("auth", PubNubUtil.joinString(authKeys, ","));
        }

        return this.getRetrofit().getAccessManagerService().audit(this.getPubnub().getConfiguration().getSubscribeKey(), queryParams);
    }

    @Override
    protected PNAccessManagerAuditResult createResponse(Response<Envelope<AccessManagerAuditPayload>> input) throws PubNubException {
        PNAccessManagerAuditResult.PNAccessManagerAuditResultBuilder pnAccessManagerAuditResult = PNAccessManagerAuditResult.builder();

        if (input.body() == null || input.body().getPayload() == null) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_PARSING_ERROR).build();
        }

        AccessManagerAuditPayload auditPayload = input.body().getPayload();
        pnAccessManagerAuditResult
                .authKeys(auditPayload.getAuthKeys())
                .channel(auditPayload.getChannel())
                .channelGroup(auditPayload.getChannelGroup())
                .level(auditPayload.getLevel())
                .subscribeKey(auditPayload.getSubscribeKey());


        return pnAccessManagerAuditResult.build();
    }

    @Override
    protected PNOperationType getOperationType() {
        return PNOperationType.PNAccessManagerAudit;
    }

    @Override
    protected boolean isAuthRequired() {
        return false;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Audit authKeys(final List<String> authKeys) {
        this.authKeys = authKeys;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Audit channel(final String channel) {
        this.channel = channel;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Audit channelGroup(final String channelGroup) {
        this.channelGroup = channelGroup;
        return this;
    }
}

package com.pubnub.api.endpoints.pubsub;

import com.pubnub.api.PubNub;
import com.pubnub.api.PubNubException;
import com.pubnub.api.PubNubUtil;
import com.pubnub.api.builder.PubNubErrorBuilder;
import com.pubnub.api.endpoints.Endpoint;
import com.pubnub.api.enums.PNOperationType;
import com.pubnub.api.managers.RetrofitManager;
import com.pubnub.api.models.server.SubscribeEnvelope;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import lombok.experimental.Accessors;
import lombok.extern.slf4j.Slf4j;
import retrofit2.Call;
import retrofit2.Response;

/**
 * Supports calling of the subscribe endpoints and deconstructs the response to POJO's.
 */
@Slf4j
@Accessors(chain = true, fluent = true)
public class Subscribe extends Endpoint<SubscribeEnvelope, SubscribeEnvelope> {

    /**
     * List of channels that will be called to subscribe.
     */
    private List<String> channels;
    /**
     * List of channel groups that will be called with subscribe.
     */
    private List<String> channelGroups;

    /**
     * timetoken to subscribe with 0 for initial subscribe.
     */
    private Long timetoken;

    /**
     * filterExpression used as part of PubSub V2 specification to filter on message.
     */
    private String filterExpression;

    /**
     * region is used as part of PubSub V2 to help the server route traffic to best data center.
     */
    private String region;

    /**
     * CreFte a new Subscribe instance endpoint.
     *
     * @param pubnub supplied pubnub instance.
     */
    public Subscribe(PubNub pubnub, RetrofitManager retrofit) {
        super(pubnub, null, retrofit);
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
    protected Call<SubscribeEnvelope> doWork(Map<String, String> params) throws PubNubException {
        String channelCSV;

        if (channelGroups.size() > 0) {
            params.put("channel-group", PubNubUtil.joinString(channelGroups, ","));
        }

        if (filterExpression != null && filterExpression.length() > 0) {
            params.put("filter-expr", PubNubUtil.urlEncode(filterExpression));
        }

        if (timetoken != null) {
            params.put("tt", timetoken.toString());
        }

        if (region != null) {
            params.put("tr", region);
        }

        if (channels.size() > 0) {
            channelCSV = PubNubUtil.joinString(channels, ",");
        } else {
            channelCSV = ",";
        }

        params.put("heartbeat", String.valueOf(this.getPubnub().getConfiguration().getPresenceTimeout()));

        return this.getRetrofit().getSubscribeService()
                .subscribe(this.getPubnub().getConfiguration().getSubscribeKey(), channelCSV, params);
    }

    @Override
    protected SubscribeEnvelope createResponse(Response<SubscribeEnvelope> input) throws PubNubException {

        if (input.body() == null) {
            throw PubNubException.builder().pubnubError(PubNubErrorBuilder.PNERROBJ_PARSING_ERROR).build();
        }

        return input.body();
    }

    @Override
    protected PNOperationType getOperationType() {
        return PNOperationType.PNSubscribeOperation;
    }

    @Override
    protected boolean isAuthRequired() {
        return true;
    }

    /**
     * List of channels that will be called to subscribe.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Subscribe channels(final List<String> channels) {
        this.channels = channels;
        return this;
    }

    /**
     * List of channel groups that will be called with subscribe.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Subscribe channelGroups(final List<String> channelGroups) {
        this.channelGroups = channelGroups;
        return this;
    }

    /**
     * timetoken to subscribe with 0 for initial subscribe.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Subscribe timetoken(final Long timetoken) {
        this.timetoken = timetoken;
        return this;
    }

    /**
     * filterExpression used as part of PubSub V2 specification to filter on message.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Subscribe filterExpression(final String filterExpression) {
        this.filterExpression = filterExpression;
        return this;
    }

    /**
     * region is used as part of PubSub V2 to help the server route traffic to best data center.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Subscribe region(final String region) {
        this.region = region;
        return this;
    }

}

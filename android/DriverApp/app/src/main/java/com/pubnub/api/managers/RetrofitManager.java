package com.pubnub.api.managers;


import com.pubnub.api.PNConfiguration;
import com.pubnub.api.PubNub;
import com.pubnub.api.endpoints.vendor.AppEngineFactory;
import com.pubnub.api.enums.PNLogVerbosity;
import com.pubnub.api.interceptors.SignatureInterceptor;
import com.pubnub.api.services.AccessManagerService;
import com.pubnub.api.services.ChannelGroupService;
import com.pubnub.api.services.HistoryService;
import com.pubnub.api.services.PresenceService;
import com.pubnub.api.services.PublishService;
import com.pubnub.api.services.PushService;
import com.pubnub.api.services.SubscribeService;
import com.pubnub.api.services.TimeService;

import java.util.Collections;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.TimeUnit;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;

public class RetrofitManager {

    private PubNub pubnub;

    private SignatureInterceptor signatureInterceptor;

    private OkHttpClient transactionClientInstance;
    private OkHttpClient subscriptionClientInstance;

    // services
    private PresenceService presenceService;
    private HistoryService historyService;
    private PushService pushService;
    private AccessManagerService accessManagerService;
    private ChannelGroupService channelGroupService;
    private TimeService timeService;
    private PublishService publishService;
    private SubscribeService subscribeService;

    private Retrofit transactionInstance;
    private Retrofit subscriptionInstance;

    public RetrofitManager(PubNub pubNubInstance) {
        this.pubnub = pubNubInstance;

        this.signatureInterceptor = new SignatureInterceptor(pubNubInstance);

        if (!pubNubInstance.getConfiguration().isGoogleAppEngineNetworking()) {
            this.transactionClientInstance = createOkHttpClient(
                    this.pubnub.getConfiguration().getNonSubscribeRequestTimeout(),
                    this.pubnub.getConfiguration().getConnectTimeout()
            );

            this.subscriptionClientInstance = createOkHttpClient(
                    this.pubnub.getConfiguration().getSubscribeTimeout(),
                    this.pubnub.getConfiguration().getConnectTimeout()
            );
        }

        this.transactionInstance = createRetrofit(this.transactionClientInstance);
        this.subscriptionInstance = createRetrofit(this.subscriptionClientInstance);

        this.presenceService = transactionInstance.create(PresenceService.class);
        this.historyService = transactionInstance.create(HistoryService.class);
        this.pushService = transactionInstance.create(PushService.class);
        this.accessManagerService = transactionInstance.create(AccessManagerService.class);
        this.channelGroupService = transactionInstance.create(ChannelGroupService.class);
        this.publishService = transactionInstance.create(PublishService.class);
        this.subscribeService = subscriptionInstance.create(SubscribeService.class);
        this.timeService = transactionInstance.create(TimeService.class);

    }

    private OkHttpClient createOkHttpClient(int requestTimeout, int connectTimeOut) {
        PNConfiguration pnConfiguration = pubnub.getConfiguration();
        OkHttpClient.Builder httpClient = new OkHttpClient.Builder();
        httpClient.readTimeout(requestTimeout, TimeUnit.SECONDS);
        httpClient.connectTimeout(connectTimeOut, TimeUnit.SECONDS);

        if (pubnub.getConfiguration().getLogVerbosity() == PNLogVerbosity.BODY) {
            HttpLoggingInterceptor logging = new HttpLoggingInterceptor();
            logging.setLevel(HttpLoggingInterceptor.Level.BODY);
            httpClient.addInterceptor(logging);
        }

        if (pubnub.getConfiguration().getHttpLoggingInterceptor() != null) {
            httpClient.addInterceptor(pubnub.getConfiguration().getHttpLoggingInterceptor());
        }

        if (pnConfiguration.getSslSocketFactory() != null && pnConfiguration.getX509ExtendedTrustManager() != null) {
            httpClient.sslSocketFactory(pnConfiguration.getSslSocketFactory(), pnConfiguration.getX509ExtendedTrustManager());
        }

        if (pnConfiguration.getConnectionSpec() != null) {
            httpClient.connectionSpecs(Collections.singletonList(pnConfiguration.getConnectionSpec()));
        }

        if (pnConfiguration.getHostnameVerifier() != null) {
            httpClient.hostnameVerifier(pnConfiguration.getHostnameVerifier());
        }

        if (pubnub.getConfiguration().getProxy() != null) {
            httpClient.proxy(pubnub.getConfiguration().getProxy());
        }

        if (pubnub.getConfiguration().getProxySelector() != null) {
            httpClient.proxySelector(pubnub.getConfiguration().getProxySelector());
        }

        if (pubnub.getConfiguration().getProxyAuthenticator() != null) {
            httpClient.proxyAuthenticator(pubnub.getConfiguration().getProxyAuthenticator());
        }

        if (pubnub.getConfiguration().getCertificatePinner() != null) {
            httpClient.certificatePinner(pubnub.getConfiguration().getCertificatePinner());
        }

        httpClient.addInterceptor(this.signatureInterceptor);

        OkHttpClient constructedClient = httpClient.build();

        if (pubnub.getConfiguration().getMaximumConnections() != null) {
            constructedClient.dispatcher().setMaxRequestsPerHost(pubnub.getConfiguration().getMaximumConnections());
        }

        return constructedClient;
    }

    private Retrofit createRetrofit(OkHttpClient client) {
        Retrofit.Builder retrofitBuilder = new Retrofit.Builder();

        if (pubnub.getConfiguration().isGoogleAppEngineNetworking()) {
            retrofitBuilder.callFactory(new AppEngineFactory.Factory(pubnub));
        }

        retrofitBuilder = retrofitBuilder
                .baseUrl(pubnub.getBaseUrl())
                .addConverterFactory(this.pubnub.getMapper().getConverterFactory());

        if (!pubnub.getConfiguration().isGoogleAppEngineNetworking()) {
            retrofitBuilder = retrofitBuilder.client(client);
        }

        return retrofitBuilder.build();
    }


    public void destroy(boolean force) {
        if (this.transactionClientInstance != null) {
            closeExecutor(this.transactionClientInstance, force);
        }
        if (this.subscriptionClientInstance != null) {
            closeExecutor(this.subscriptionClientInstance, force);
        }
    }

    private void closeExecutor(OkHttpClient client, boolean force) {
        client.dispatcher().cancelAll();
        if (force) {
            client.connectionPool().evictAll();
            ExecutorService executorService = client.dispatcher().executorService();
            executorService.shutdown();
        }
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PresenceService getPresenceService() {
        return this.presenceService;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public HistoryService getHistoryService() {
        return this.historyService;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PushService getPushService() {
        return this.pushService;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public AccessManagerService getAccessManagerService() {
        return this.accessManagerService;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public ChannelGroupService getChannelGroupService() {
        return this.channelGroupService;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public TimeService getTimeService() {
        return this.timeService;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PublishService getPublishService() {
        return this.publishService;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public SubscribeService getSubscribeService() {
        return this.subscribeService;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Retrofit getTransactionInstance() {
        return this.transactionInstance;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Retrofit getSubscriptionInstance() {
        return this.subscriptionInstance;
    }
}

package com.pubnub.api;


import com.pubnub.api.enums.PNHeartbeatNotificationOptions;
import com.pubnub.api.enums.PNLogVerbosity;
import com.pubnub.api.enums.PNReconnectionPolicy;

import java.net.Proxy;
import java.net.ProxySelector;
import java.util.UUID;

import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.SSLSocketFactory;
import javax.net.ssl.X509ExtendedTrustManager;

import lombok.AccessLevel;
import lombok.Getter;
import lombok.Setter;
import lombok.experimental.Accessors;
import okhttp3.Authenticator;
import okhttp3.CertificatePinner;
import okhttp3.ConnectionSpec;
import okhttp3.logging.HttpLoggingInterceptor;

@Getter
@Setter
@Accessors(chain = true)

public class PNConfiguration {
    private static final int DEFAULT_DEDUPE_SIZE = 100;
    private static final int PRESENCE_TIMEOUT = 300;
    private static final int NON_SUBSCRIBE_REQUEST_TIMEOUT = 10;
    private static final int SUBSCRIBE_TIMEOUT = 310;
    private static final int CONNECT_TIMEOUT = 5;

    private SSLSocketFactory sslSocketFactory;
    private X509ExtendedTrustManager x509ExtendedTrustManager;
    private ConnectionSpec connectionSpec;

    private HostnameVerifier hostnameVerifier;

    /**
     * Set to true to send a UUID for PubNub instance
     */
    private boolean includeInstanceIdentifier;

    /**
     * Set to true to send a UUID on each request
     */
    private boolean includeRequestIdentifier;

    /**
     * By default, the origin is pointing directly to PubNub servers. If a proxy origin is needed, set a custom
     * origin using this parameter.
     */
    private String origin;
    private int subscribeTimeout;


    /**
     * In seconds, how long the server will consider this client to be online before issuing a leave event.
     */
    @Setter(AccessLevel.NONE)
    private int presenceTimeout;
    /**
     * In seconds, How often the client should announce it's existence via heartbeating.
     */
    @Setter(AccessLevel.NONE)
    private int heartbeatInterval;

    /**
     * set to true to switch the client to HTTPS:// based communications.
     */
    private boolean secure;
    /**
     * Subscribe Key provided by PubNub
     */
    private String subscribeKey;
    /**
     * Publish Key provided by PubNub.
     */
    private String publishKey;
    private String secretKey;
    private String cipherKey;
    private String authKey;
    private String uuid;
    /**
     * If proxies are forcefully caching requests, set to true to allow the client to randomize the subdomain.
     * This configuration is not supported if custom origin is enabled.
     */
    @Deprecated
    private boolean cacheBusting;

    /**
     * toggle to enable verbose logging.
     */

    private PNLogVerbosity logVerbosity;

    /**
     * Stores the maximum number of seconds which the client should wait for connection before timing out.
     */
    private int connectTimeout;

    /**
     * Reference on number of seconds which is used by client during non-subscription operations to
     * check whether response potentially failed with 'timeout' or not.
     */
    private int nonSubscribeRequestTimeout;

    /**
     * Supress leave events when a channel gets disconnected
     */
    private boolean supressLeaveEvents;

    /**
     * verbosity of heartbeat configuration, by default only alerts on failed heartbeats
     */
    private PNHeartbeatNotificationOptions heartbeatNotificationOptions;

    /**
     * filterExpression used as part of PSV2 specification.
     */
    private String filterExpression;


    /**
     * Reconnection policy which will be used if/when networking goes down
     */
    private PNReconnectionPolicy reconnectionPolicy;

    /**
     * Set how many times the reconneciton manager will try to connect before giving app
     */
    private int maximumReconnectionRetries;

    /**
     * Proxy configuration which will be passed to the networking layer.
     */
    private Proxy proxy;
    private ProxySelector proxySelector;
    private Authenticator proxyAuthenticator;

    private CertificatePinner certificatePinner;

    private Integer maximumConnections;

    private HttpLoggingInterceptor httpLoggingInterceptor;

    /**
     * if set, the SDK will alert once the number of messages arrived in one call equal to the threshold
     */
    private Integer requestMessageCountThreshold;

    /**
     * Use Google App Engine based networking configuration
     */
    private boolean googleAppEngineNetworking;
    private boolean startSubscriberThread;

    private boolean dedupOnSubscribe;
    private Integer maximumMessagesCacheSize;

    /**
     * Initialize the PNConfiguration with default values
     */
    public PNConfiguration() {
        setPresenceTimeout(PRESENCE_TIMEOUT);

        uuid = "pn-" + UUID.randomUUID().toString();

        nonSubscribeRequestTimeout = NON_SUBSCRIBE_REQUEST_TIMEOUT;
        subscribeTimeout = SUBSCRIBE_TIMEOUT;
        connectTimeout = CONNECT_TIMEOUT;

        logVerbosity = PNLogVerbosity.NONE;

        heartbeatNotificationOptions = PNHeartbeatNotificationOptions.FAILURES;
        reconnectionPolicy = PNReconnectionPolicy.NONE;

        secure = true;

        includeInstanceIdentifier = false;

        includeRequestIdentifier = true;

        startSubscriberThread = true;

        maximumReconnectionRetries = -1;

        dedupOnSubscribe = false;
        supressLeaveEvents = false;
        maximumMessagesCacheSize = DEFAULT_DEDUPE_SIZE;
    }

    /**
     * set presence configurations for timeout and announce interval.
     *
     * @param timeout  presence timeout; how long before the server considers this client to be gone.
     * @param interval presence announce interval, how often the client should announce itself.
     * @return returns itself.
     */
    public PNConfiguration setPresenceTimeoutWithCustomInterval(int timeout, int interval) {
        this.presenceTimeout = timeout;
        this.heartbeatInterval = interval;

        return this;
    }

    /**
     * set presence configurations for timeout and allow the client to pick the best interval
     *
     * @param timeout presence timeout; how long before the server considers this client to be gone.
     * @return returns itself.
     */
    public PNConfiguration setPresenceTimeout(int timeout) {
        return setPresenceTimeoutWithCustomInterval(timeout, (timeout / 2) - 1);
    }

    /**
     * By default, the origin is pointing directly to PubNub servers. If a proxy origin is needed, set a custom
     * origin using this parameter.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getOrigin() {
        return this.origin;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getSubscribeTimeout() {
        return this.subscribeTimeout;
    }

    /**
     * In seconds, how long the server will consider this client to be online before issuing a leave event.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getPresenceTimeout() {
        return this.presenceTimeout;
    }

    /**
     * In seconds, How often the client should announce it's existence via heartbeating.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getHeartbeatInterval() {
        return this.heartbeatInterval;
    }

    /**
     * set to true to switch the client to HTTPS:// based communications.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isSecure() {
        return this.secure;
    }

    /**
     * Subscribe Key provided by PubNub
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSubscribeKey() {
        return this.subscribeKey;
    }

    /**
     * Publish Key provided by PubNub.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getPublishKey() {
        return this.publishKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSecretKey() {
        return this.secretKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getCipherKey() {
        return this.cipherKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getAuthKey() {
        return this.authKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getUuid() {
        return this.uuid;
    }

    /**
     * If proxies are forcefully caching requests, set to true to allow the client to randomize the subdomain.
     * This configuration is not supported if custom origin is enabled.
     */
    @java.lang.Deprecated
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isCacheBusting() {
        return this.cacheBusting;
    }

    /**
     * toggle to enable verbose logging.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNLogVerbosity getLogVerbosity() {
        return this.logVerbosity;
    }

    /**
     * Stores the maximum number of seconds which the client should wait for connection before timing out.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getConnectTimeout() {
        return this.connectTimeout;
    }

    /**
     * Reference on number of seconds which is used by client during non-subscription operations to
     * check whether response potentially failed with 'timeout' or not.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getNonSubscribeRequestTimeout() {
        return this.nonSubscribeRequestTimeout;
    }

    /**
     * Supress leave events when a channel gets disconnected
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isSupressLeaveEvents() {
        return this.supressLeaveEvents;
    }

    /**
     * verbosity of heartbeat configuration, by default only alerts on failed heartbeats
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNHeartbeatNotificationOptions getHeartbeatNotificationOptions() {
        return this.heartbeatNotificationOptions;
    }

    /**
     * filterExpression used as part of PSV2 specification.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getFilterExpression() {
        return this.filterExpression;
    }

    /**
     * Reconnection policy which will be used if/when networking goes down
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNReconnectionPolicy getReconnectionPolicy() {
        return this.reconnectionPolicy;
    }

    /**
     * Set how many times the reconneciton manager will try to connect before giving app
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getMaximumReconnectionRetries() {
        return this.maximumReconnectionRetries;
    }

    /**
     * Proxy configuration which will be passed to the networking layer.
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Proxy getProxy() {
        return this.proxy;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public ProxySelector getProxySelector() {
        return this.proxySelector;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Authenticator getProxyAuthenticator() {
        return this.proxyAuthenticator;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public CertificatePinner getCertificatePinner() {
        return this.certificatePinner;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Integer getMaximumConnections() {
        return this.maximumConnections;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public HttpLoggingInterceptor getHttpLoggingInterceptor() {
        return this.httpLoggingInterceptor;
    }

    /**
     * if set, the SDK will alert once the number of messages arrived in one call equal to the threshold
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Integer getRequestMessageCountThreshold() {
        return this.requestMessageCountThreshold;
    }

    /**
     * Use Google App Engine based networking configuration
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isGoogleAppEngineNetworking() {
        return this.googleAppEngineNetworking;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isStartSubscriberThread() {
        return this.startSubscriberThread;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isDedupOnSubscribe() {
        return this.dedupOnSubscribe;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Integer getMaximumMessagesCacheSize() {
        return this.maximumMessagesCacheSize;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setSslSocketFactory(final SSLSocketFactory sslSocketFactory) {
        this.sslSocketFactory = sslSocketFactory;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setX509ExtendedTrustManager(final X509ExtendedTrustManager x509ExtendedTrustManager) {
        this.x509ExtendedTrustManager = x509ExtendedTrustManager;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setConnectionSpec(final ConnectionSpec connectionSpec) {
        this.connectionSpec = connectionSpec;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setHostnameVerifier(final HostnameVerifier hostnameVerifier) {
        this.hostnameVerifier = hostnameVerifier;
        return this;
    }

    /**
     * Set to true to send a UUID for PubNub instance
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setIncludeInstanceIdentifier(final boolean includeInstanceIdentifier) {
        this.includeInstanceIdentifier = includeInstanceIdentifier;
        return this;
    }

    /**
     * Set to true to send a UUID on each request
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setIncludeRequestIdentifier(final boolean includeRequestIdentifier) {
        this.includeRequestIdentifier = includeRequestIdentifier;
        return this;
    }

    /**
     * By default, the origin is pointing directly to PubNub servers. If a proxy origin is needed, set a custom
     * origin using this parameter.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setOrigin(final String origin) {
        this.origin = origin;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setSubscribeTimeout(final int subscribeTimeout) {
        this.subscribeTimeout = subscribeTimeout;
        return this;
    }

    /**
     * set to true to switch the client to HTTPS:// based communications.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setSecure(final boolean secure) {
        this.secure = secure;
        return this;
    }

    /**
     * Subscribe Key provided by PubNub
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setSubscribeKey(final String subscribeKey) {
        this.subscribeKey = subscribeKey;
        return this;
    }

    /**
     * Publish Key provided by PubNub.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setPublishKey(final String publishKey) {
        this.publishKey = publishKey;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setSecretKey(final String secretKey) {
        this.secretKey = secretKey;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setCipherKey(final String cipherKey) {
        this.cipherKey = cipherKey;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setAuthKey(final String authKey) {
        this.authKey = authKey;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setUuid(final String uuid) {
        this.uuid = uuid;
        return this;
    }

    /**
     * If proxies are forcefully caching requests, set to true to allow the client to randomize the subdomain.
     * This configuration is not supported if custom origin is enabled.
     *
     * @return this
     */
    @java.lang.Deprecated
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setCacheBusting(final boolean cacheBusting) {
        this.cacheBusting = cacheBusting;
        return this;
    }

    /**
     * toggle to enable verbose logging.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setLogVerbosity(final PNLogVerbosity logVerbosity) {
        this.logVerbosity = logVerbosity;
        return this;
    }

    /**
     * Stores the maximum number of seconds which the client should wait for connection before timing out.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setConnectTimeout(final int connectTimeout) {
        this.connectTimeout = connectTimeout;
        return this;
    }

    /**
     * Reference on number of seconds which is used by client during non-subscription operations to
     * check whether response potentially failed with 'timeout' or not.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setNonSubscribeRequestTimeout(final int nonSubscribeRequestTimeout) {
        this.nonSubscribeRequestTimeout = nonSubscribeRequestTimeout;
        return this;
    }

    /**
     * Supress leave events when a channel gets disconnected
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setSupressLeaveEvents(final boolean supressLeaveEvents) {
        this.supressLeaveEvents = supressLeaveEvents;
        return this;
    }

    /**
     * verbosity of heartbeat configuration, by default only alerts on failed heartbeats
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setHeartbeatNotificationOptions(final PNHeartbeatNotificationOptions heartbeatNotificationOptions) {
        this.heartbeatNotificationOptions = heartbeatNotificationOptions;
        return this;
    }

    /**
     * if set, the SDK will alert once the number of messages arrived in one call equal to the threshold
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setRequestMessageCountThreshold(final Integer requestMessageCountThreshold) {
        this.requestMessageCountThreshold = requestMessageCountThreshold;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public SSLSocketFactory getSslSocketFactory() {
        return this.sslSocketFactory;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public X509ExtendedTrustManager getX509ExtendedTrustManager() {
        return this.x509ExtendedTrustManager;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public ConnectionSpec getConnectionSpec() {
        return this.connectionSpec;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public HostnameVerifier getHostnameVerifier() {
        return this.hostnameVerifier;
    }

    /**
     * Set to true to send a UUID for PubNub instance
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isIncludeInstanceIdentifier() {
        return this.includeInstanceIdentifier;
    }

    /**
     * Set to true to send a UUID on each request
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isIncludeRequestIdentifier() {
        return this.includeRequestIdentifier;
    }

    /**
     * filterExpression used as part of PSV2 specification.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setFilterExpression(final String filterExpression) {
        this.filterExpression = filterExpression;
        return this;
    }

    /**
     * Reconnection policy which will be used if/when networking goes down
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setReconnectionPolicy(final PNReconnectionPolicy reconnectionPolicy) {
        this.reconnectionPolicy = reconnectionPolicy;
        return this;
    }

    /**
     * Set how many times the reconneciton manager will try to connect before giving app
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setMaximumReconnectionRetries(final int maximumReconnectionRetries) {
        this.maximumReconnectionRetries = maximumReconnectionRetries;
        return this;
    }

    /**
     * Proxy configuration which will be passed to the networking layer.
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setProxy(final Proxy proxy) {
        this.proxy = proxy;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setProxySelector(final ProxySelector proxySelector) {
        this.proxySelector = proxySelector;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setProxyAuthenticator(final Authenticator proxyAuthenticator) {
        this.proxyAuthenticator = proxyAuthenticator;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setCertificatePinner(final CertificatePinner certificatePinner) {
        this.certificatePinner = certificatePinner;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setMaximumConnections(final Integer maximumConnections) {
        this.maximumConnections = maximumConnections;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setHttpLoggingInterceptor(final HttpLoggingInterceptor httpLoggingInterceptor) {
        this.httpLoggingInterceptor = httpLoggingInterceptor;
        return this;
    }

    /**
     * Use Google App Engine based networking configuration
     *
     * @return this
     */
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setGoogleAppEngineNetworking(final boolean googleAppEngineNetworking) {
        this.googleAppEngineNetworking = googleAppEngineNetworking;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setStartSubscriberThread(final boolean startSubscriberThread) {
        this.startSubscriberThread = startSubscriberThread;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setDedupOnSubscribe(final boolean dedupOnSubscribe) {
        this.dedupOnSubscribe = dedupOnSubscribe;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNConfiguration setMaximumMessagesCacheSize(final Integer maximumMessagesCacheSize) {
        this.maximumMessagesCacheSize = maximumMessagesCacheSize;
        return this;
    }

}

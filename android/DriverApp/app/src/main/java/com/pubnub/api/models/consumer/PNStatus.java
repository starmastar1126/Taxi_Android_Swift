package com.pubnub.api.models.consumer;

import com.pubnub.api.endpoints.Endpoint;
import com.pubnub.api.enums.PNOperationType;
import com.pubnub.api.enums.PNStatusCategory;

import java.util.List;

import lombok.AccessLevel;
import lombok.Getter;

public class PNStatus {

    private PNStatusCategory category;
    private PNErrorData errorData;
    private boolean error;

    // boolean automaticallyRetry;

    private int statusCode;
    private PNOperationType operation;

    private boolean tlsEnabled;

    private String uuid;
    private String authKey;
    private String origin;
    private Object clientRequest;

    // send back channel, channel groups that were affected by this operation
    private List<String> affectedChannels;
    private List<String> affectedChannelGroups;

    @Getter(AccessLevel.NONE)
    private Endpoint executedEndpoint;


    public void retry() {
        executedEndpoint.retry();
    }

    //    @java.beans.ConstructorProperties({"category", "errorData", "error", "statusCode", "operation", "tlsEnabled", "uuid", "authKey", "origin", "clientRequest", "affectedChannels", "affectedChannelGroups", "executedEndpoint"})
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNStatus(final PNStatusCategory category, final PNErrorData errorData, final boolean error, final int statusCode, final PNOperationType operation, final boolean tlsEnabled, final String uuid, final String authKey, final String origin, final Object clientRequest, final List<String> affectedChannels, final List<String> affectedChannelGroups, final Endpoint executedEndpoint) {
        this.category = category;
        this.errorData = errorData;
        this.error = error;
        this.statusCode = statusCode;
        this.operation = operation;
        this.tlsEnabled = tlsEnabled;
        this.uuid = uuid;
        this.authKey = authKey;
        this.origin = origin;
        this.clientRequest = clientRequest;
        this.affectedChannels = affectedChannels;
        this.affectedChannelGroups = affectedChannelGroups;
        this.executedEndpoint = executedEndpoint;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNStatusBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private PNStatusCategory category;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private PNErrorData errorData;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private boolean error;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private int statusCode;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private PNOperationType operation;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private boolean tlsEnabled;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String uuid;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String authKey;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String origin;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Object clientRequest;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> affectedChannels;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> affectedChannelGroups;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Endpoint executedEndpoint;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNStatusBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder category(final PNStatusCategory category) {
            this.category = category;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder errorData(final PNErrorData errorData) {
            this.errorData = errorData;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder error(final boolean error) {
            this.error = error;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder statusCode(final int statusCode) {
            this.statusCode = statusCode;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder operation(final PNOperationType operation) {
            this.operation = operation;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder tlsEnabled(final boolean tlsEnabled) {
            this.tlsEnabled = tlsEnabled;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder uuid(final String uuid) {
            this.uuid = uuid;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder authKey(final String authKey) {
            this.authKey = authKey;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder origin(final String origin) {
            this.origin = origin;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder clientRequest(final Object clientRequest) {
            this.clientRequest = clientRequest;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder affectedChannels(final List<String> affectedChannels) {
            this.affectedChannels = affectedChannels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder affectedChannelGroups(final List<String> affectedChannelGroups) {
            this.affectedChannelGroups = affectedChannelGroups;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatusBuilder executedEndpoint(final Endpoint executedEndpoint) {
            this.executedEndpoint = executedEndpoint;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNStatus build() {
            return new PNStatus(category, errorData, error, statusCode, operation, tlsEnabled, uuid, authKey, origin, clientRequest, affectedChannels, affectedChannelGroups, executedEndpoint);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNStatus.PNStatusBuilder(category=" + this.category + ", errorData=" + this.errorData + ", error=" + this.error + ", statusCode=" + this.statusCode + ", operation=" + this.operation + ", tlsEnabled=" + this.tlsEnabled + ", uuid=" + this.uuid + ", authKey=" + this.authKey + ", origin=" + this.origin + ", clientRequest=" + this.clientRequest + ", affectedChannels=" + this.affectedChannels + ", affectedChannelGroups=" + this.affectedChannelGroups + ", executedEndpoint=" + this.executedEndpoint + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNStatusBuilder builder() {
        return new PNStatusBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNStatusCategory getCategory() {
        return this.category;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNErrorData getErrorData() {
        return this.errorData;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isError() {
        return this.error;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getStatusCode() {
        return this.statusCode;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNOperationType getOperation() {
        return this.operation;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isTlsEnabled() {
        return this.tlsEnabled;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getUuid() {
        return this.uuid;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getAuthKey() {
        return this.authKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getOrigin() {
        return this.origin;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Object getClientRequest() {
        return this.clientRequest;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getAffectedChannels() {
        return this.affectedChannels;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getAffectedChannelGroups() {
        return this.affectedChannelGroups;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNStatus(category=" + this.getCategory() + ", errorData=" + this.getErrorData() + ", error=" + this.isError() + ", statusCode=" + this.getStatusCode() + ", operation=" + this.getOperation() + ", tlsEnabled=" + this.isTlsEnabled() + ", uuid=" + this.getUuid() + ", authKey=" + this.getAuthKey() + ", origin=" + this.getOrigin() + ", clientRequest=" + this.getClientRequest() + ", affectedChannels=" + this.getAffectedChannels() + ", affectedChannelGroups=" + this.getAffectedChannelGroups() + ", executedEndpoint=" + this.executedEndpoint + ")";
    }

    /*
    public void cancelAutomaticRetry() {
        // TODO
    }
    */

}

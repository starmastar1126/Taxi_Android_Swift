package com.pubnub.api.endpoints.vendor;

import com.pubnub.api.PubNub;
import com.pubnub.api.PubNubUtil;

import java.io.IOException;
import java.net.HttpURLConnection;
import java.net.URL;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.Headers;
import okhttp3.MediaType;
import okhttp3.Protocol;
import okhttp3.Request;
import okhttp3.Response;
import okhttp3.ResponseBody;
import okio.BufferedSink;
import okio.BufferedSource;
import okio.Okio;

public class AppEngineFactory implements Call {
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    private static final java.util.logging.Logger log = java.util.logging.Logger.getLogger(AppEngineFactory.class.getName());

    private Request request;
    private PubNub pubNub;

    AppEngineFactory(Request request, PubNub pubNub) {
        this.request = request;
        this.pubNub = pubNub;
    }

    @Override
    public Request request() {
        return request;
    }

    @Override
    public Response execute() throws IOException {
        request = PubNubUtil.requestSigner(request, pubNub.getConfiguration(), pubNub.getTimestamp());

        URL url = request.url().url();
        final HttpURLConnection connection = (HttpURLConnection) url.openConnection();
        connection.setUseCaches(false);
        connection.setDoOutput(true);
        connection.setRequestMethod(request.method());

        Headers headers = request.headers();
        if (headers != null) {
            for (int i = 0; i < headers.size(); i++) {
                String name = headers.name(i);
                connection.setRequestProperty(name, headers.get(name));
            }
        }

        if (request.body() != null) {
            BufferedSink outbuf;
            outbuf = Okio.buffer(Okio.sink(connection.getOutputStream()));
            request.body().writeTo(outbuf);
            outbuf.close();
        }

        connection.connect();

        final BufferedSource source = Okio.buffer(Okio.source(connection.getInputStream()));
        if (connection.getResponseCode() != 200) {
            throw new IOException("Fail to call " + " :: " + source.readUtf8());
        }
        Response response = new Response.Builder()
                .code(connection.getResponseCode())
                .message(connection.getResponseMessage())
                .request(request)
                .protocol(Protocol.HTTP_1_1)
                .body(new ResponseBody() {
                    @Override
                    public MediaType contentType() {
                        return MediaType.parse(connection.getContentType());
                    }

                    @Override
                    public long contentLength() {
                        return connection.getContentLengthLong();
                    }

                    @Override
                    public BufferedSource source() {
                        return source;
                    }
                })
                .build();
        return response;
    }

    @Override
    public void enqueue(Callback responseCallback) {

    }

    @Override
    public void cancel() {

    }

    @Override
    public boolean isExecuted() {
        return false;
    }

    @Override
    public boolean isCanceled() {
        return false;
    }

    @Override
    public Call clone() {
        try {
            return (Call) super.clone();
        } catch (CloneNotSupportedException e) {
            return null;
        }
    }

    public static class Factory implements Call.Factory {
        private PubNub pubNub;

        public Factory(PubNub pubNub) {
            this.pubNub = pubNub;
        }

        @Override
        public Call newCall(Request request) {
            return new AppEngineFactory(request, pubNub);
        }
    }
}

package com.pubnub.api.services;

import java.util.List;
import java.util.Map;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Path;
import retrofit2.http.QueryMap;

public interface PushService {

    @GET("v1/push/sub-key/{subKey}/devices/{pushToken}")
    Call<List<Object>> modifyChannelsForDevice(@Path("subKey") String subKey,
                                               @Path("pushToken") String pushToken,
                                               @QueryMap Map<String, String> options);

    @GET("v1/push/sub-key/{subKey}/devices/{pushToken}/remove")
    Call<List<Object>> removeAllChannelsForDevice(@Path("subKey") String subKey,
                                                  @Path("pushToken") String pushToken,
                                                  @QueryMap Map<String, String> options);

    @GET("v1/push/sub-key/{subKey}/devices/{pushToken}")
    Call<List<String>> listChannelsForDevice(@Path("subKey") String subKey,
                                             @Path("pushToken") String pushToken,
                                             @QueryMap Map<String, String> options);

}

package com.pubnub.api.services;

import java.util.List;
import java.util.Map;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.QueryMap;

public interface TimeService {
    @GET("/time/0")
    Call<List<Long>> fetchTime(@QueryMap Map<String, String> options);
}

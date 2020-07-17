package com.view.pinnedListView;

public class CountryListItem {
	public static final int ITEM = 0;
	public static final int SECTION = 1;

	public final int type;
	public final String text;

	public int sectionPosition;
	public int listPosition;
	public int CountSubItems;

	public String vCountryCode ="";
	public String vPhoneCode ="";
	public String iCountryId ="";

	public String iStateId ="";
	public String vStateCode ="";

	public String getvCountryCode() {
		return vCountryCode;
	}

	public void setvCountryCode(String vCountryCode) {
		this.vCountryCode = vCountryCode;
	}

	public String getvPhoneCode() {
		return vPhoneCode;
	}

	public void setvPhoneCode(String vPhoneCode) {
		this.vPhoneCode = vPhoneCode;
	}

	public String getiCountryId() {
		return iCountryId;
	}

	public void setiCountryId(String iCountryId) {
		this.iCountryId = iCountryId;
	}

	public CountryListItem(int type, String text) {
		this.type = type;
		this.text = text;
	}

	@Override
	public String toString() {
		return text;
	}


	public void setvStateCode(String vStateCode) {
		this.vStateCode = vStateCode;
	}

	public String getvStateCode() {
		return vStateCode;
	}

	public void setiStateId(String iStateId) {
		this.iStateId = iStateId;
	}

	public String getiStateId() {
		return iStateId;
	}
}

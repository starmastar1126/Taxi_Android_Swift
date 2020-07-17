package com.view.pinnedListView;

public class CategoryListItem {
	public static final int ITEM = 0;
	public static final int SECTION = 1;

	public final int type;
	public final String text;

	public int sectionPosition;
	public int listPosition;
	public int CountSubItems;

	public String vTitle = "";
	public String iVehicleCategoryId = "";
	public String vCategory = "";


	public CategoryListItem(int type, String text) {
		this.type = type;
		this.text = text;
	}

	public static int getITEM() {
		return ITEM;
	}

	public static int getSECTION() {
		return SECTION;
	}

	public int getType() {
		return type;
	}

	public String getText() {
		return text;
	}

	public int getSectionPosition() {
		return sectionPosition;
	}

	public void setSectionPosition(int sectionPosition) {
		this.sectionPosition = sectionPosition;
	}

	public int getListPosition() {
		return listPosition;
	}

	public void setListPosition(int listPosition) {
		this.listPosition = listPosition;
	}

	public int getCountSubItems() {
		return CountSubItems;
	}

	public void setCountSubItems(int countSubItems) {
		CountSubItems = countSubItems;
	}

	public String getvTitle() {
		return vTitle;
	}

	public void setvTitle(String vTitle) {
		this.vTitle = vTitle;
	}

	public String getiVehicleCategoryId() {
		return iVehicleCategoryId;
	}

	public void setiVehicleCategoryId(String iVehicleCategoryId) {
		this.iVehicleCategoryId = iVehicleCategoryId;
	}

	public String getvCategory() {
		return vCategory;
	}

	public void setvCategory(String vCategory) {
		this.vCategory = vCategory;
	}

	@Override
	public String toString() {
		return text;
	}
}



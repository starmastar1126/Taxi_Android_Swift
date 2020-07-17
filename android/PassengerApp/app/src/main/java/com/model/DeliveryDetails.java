package com.model;

import java.io.Serializable;

/**
 * Created by Admin on 20-02-2017.
 */
public class DeliveryDetails implements Serializable {
    String recipientName, recipientPhoneNumber, recipientPhoneCode, recipientEmailAddress;
    String packageTypeId, vPackageTypeName;
    String pickupInstruction, deliveryInstruction, packageDetails;

    // Old Keys

    String recipientId, recipientAddress, deleteDeliverDetailLbl, yesLbl, noLbl, shippmentDetailTxt, additionalNotes, recipientvLatitude, recipientvLongitude;

    public String getYesLbl() {
        return yesLbl;
    }

    public void setYesLbl(String yesLbl) {
        this.yesLbl = yesLbl;
    }

    public String getRecipientvLatitude() {
        return recipientvLatitude;
    }

    public void setRecipientvLatitude(String recipientvLatitude) {
        this.recipientvLatitude = recipientvLatitude;
    }

    public String getRecipientvLongitude() {
        return recipientvLongitude;
    }

    public void setRecipientvLongitude(String recipientvLongitude) {
        this.recipientvLongitude = recipientvLongitude;
    }

    public String getNoLbl() {
        return noLbl;
    }

    public void setNoLbl(String noLbl) {
        this.noLbl = noLbl;
    }

    public String getDeleteDeliverDetailLbl() {
        return deleteDeliverDetailLbl;
    }

    public void setDeleteDeliverDetailLbl(String deleteDeliverDetailLbl) {
        this.deleteDeliverDetailLbl = deleteDeliverDetailLbl;
    }


    public String getRecipientId() {
        return recipientId.trim();
    }

    public void setRecipientId(String recipientId) {
        this.recipientId = recipientId;
    }

    public String getAdditionalNotes() {
        return additionalNotes;
    }

    public void setAdditionalNotes(String additionalNotes) {
        this.additionalNotes = additionalNotes;
    }


    public String getShippmentDetailTxt() {
        return shippmentDetailTxt;
    }

    public void setShippmentDetailTxt(String shippmentDetailTxt) {
        this.shippmentDetailTxt = shippmentDetailTxt;


    }

    public String getRecipientAddress() {
        return recipientAddress;
    }

    public void setRecipientAddress(String recipientAddress) {
        this.recipientAddress = recipientAddress;
    }


    public String getRecipientName() {
        return recipientName;
    }

    public void setRecipientName(String recipientName) {
        this.recipientName = recipientName;
    }

    public String getPickupInstruction() {
        return pickupInstruction;
    }

    public void setPickupInstruction(String pickupInstruction) {
        this.pickupInstruction = pickupInstruction;
    }

    public String getDeliveryInstruction() {
        return deliveryInstruction;
    }

    public void setDeliveryInstruction(String deliveryInstruction) {
        this.deliveryInstruction = deliveryInstruction;
    }

    public String getPackageDetails() {
        return packageDetails;
    }

    public void setPackageDetails(String packageDetails) {
        this.packageDetails = packageDetails;
    }

    public String getRecipientPhoneNumber() {
        return recipientPhoneNumber;
    }

    public void setRecipientPhoneNumber(String recipientPhoneNumber) {
        this.recipientPhoneNumber = recipientPhoneNumber;
    }

    public String getRecipientPhoneCode() {
        return recipientPhoneCode;
    }

    public void setRecipientPhoneCode(String recipientPhoneCode) {
        this.recipientPhoneCode = recipientPhoneCode;
    }

    public String getRecipientEmailAddress() {
        return recipientEmailAddress;
    }

    public void setRecipientEmailAddress(String recipientEmailAddress) {
        this.recipientEmailAddress = recipientEmailAddress;
    }

    public String getPackageTypeId() {
        return packageTypeId;
    }

    public void setPackageTypeId(String packageTypeId) {
        this.packageTypeId = packageTypeId;
    }

    public String getvPackageTypeName() {
        return vPackageTypeName;
    }

    public void setvPackageTypeName(String vPackageTypeName) {
        this.vPackageTypeName = vPackageTypeName;
    }


}

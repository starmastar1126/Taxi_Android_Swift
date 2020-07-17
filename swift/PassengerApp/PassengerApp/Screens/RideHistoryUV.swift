//
//  RideHistroyUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 13/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class RideHistoryUV: UIViewController, UITableViewDataSource, UITableViewDelegate, MyBtnClickDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    
    let generalFunc = GeneralFunctions()
    
    var HISTORY_TYPE = "PAST"
    
    var loaderView:UIView!
    
    var dataArrList = [NSDictionary]()
    var nextPage_str = 1
    var isLoadingMore = false
    var isNextPageAvail = false
    
    var APP_TYPE = ""
    
    var cntView:UIView!
    
    var extraHeightContainer = [CGFloat]()
    var userProfileJson:NSDictionary!
    
    var isFirstCallFinished = false
    
    var isDataLoaded = false
    
    var msgLbl:MyLabel!
    
    var isDirectPush = false
    var isSafeAreaSet = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
        if(!isDirectPush){
            pageTabBarItem.titleColor = UIColor(hex: 0x141414)
        }
        
        if(HISTORY_TYPE != "PAST" && loaderView != nil && self.isFirstCallFinished == true){
            self.dataArrList.removeAll()
            self.extraHeightContainer.removeAll()
            self.isLoadingMore = false
            self.nextPage_str = 1
            self.isNextPageAvail = false
            self.tableView.reloadData()
            
            if(self.msgLbl != nil){
                self.msgLbl.isHidden = true
            }
            
            self.getDtata(isLoadingMore: false)
        }
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        if(!isDirectPush){
            pageTabBarItem.titleColor = UIColor(hex: 0x737373)
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

         cntView = self.generalFunc.loadView(nibName: "RideHistoryScreenDesign", uv: self, contentView: contentView)
        
        self.contentView.addSubview(cntView)
        
        self.tableView.delegate = self
        self.tableView.bounces = false
        self.tableView.dataSource = self
        self.tableView.tableFooterView = UIView()
        self.tableView.register(UINib(nibName: "RideHistoryUFXListTVCell", bundle: nil), forCellReuseIdentifier: "RideHistoryUFXListTVCell")
        self.tableView.register(UINib(nibName: "RideHistoryListTVCell", bundle: nil), forCellReuseIdentifier: "RideHistoryTVCell")
        
        self.tableView.contentInset = UIEdgeInsetsMake(6, 0, GeneralFunctions.getSafeAreaInsets().bottom + 6, 0)

        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        APP_TYPE = userProfileJson.get("APP_TYPE")
        
        if(isDirectPush){
            self.addBackBarBtn()
            
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Your trips", key: "LBL_YOUR_TRIPS")
            self.title = self.generalFunc.getLanguageLabel(origValue: "Your trips", key: "LBL_YOUR_TRIPS")
        }
        
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isDataLoaded == false){
            
//            DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//                self.cntView.frame = self.view.frame
//                self.cntView.frame.size = CGSize(width: Application.screenSize.width, height: self.view.frame.height)
//                self.cntView.setNeedsLayout()
//
//
//            })

            self.extraHeightContainer.removeAll()
            self.dataArrList.removeAll()
            self.tableView.reloadData()
            self.getDtata(isLoadingMore: self.isLoadingMore)
            
            isDataLoaded = true
        }
    }
    
    
    override func viewDidLayoutSubviews() {
        
        if(isSafeAreaSet == false){
            
            if(cntView != nil){
                self.cntView.frame = self.view.frame
                cntView.frame.size.height = cntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            }
            
            isSafeAreaSet = true
        }
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func getDtata(isLoadingMore:Bool){
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.view)
            loaderView.backgroundColor = UIColor.clear
        }else if(loaderView != nil && isLoadingMore == false){
            loaderView.isHidden = false
        }

        
        let parameters = ["type": HISTORY_TYPE == "PAST" ? "getRideHistory" : "checkBookings", "UserType": Utils.appUserType, "iUserId": GeneralFunctions.getMemberd(), "page": self.nextPage_str.description]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let dataArr = dataDict.getArrObj(Utils.message_str)
                    
                    if(self.isFirstCallFinished == false){
                        self.isFirstCallFinished = true
                    }
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        self.dataArrList += [dataTemp]
                        
                        if(self.HISTORY_TYPE != "PAST"){

                            let sourceAddHeight = dataTemp.get("vSourceAddresss").height(withConstrainedWidth: Application.screenSize.width - 106, font: UIFont(name: "Roboto-Light", size: 16)!) - 20
                            var destAddHeight = dataTemp.get("tDestAddress").height(withConstrainedWidth: Application.screenSize.width - 106, font: UIFont(name: "Roboto-Light", size: 16)!)
//                            - 20
                            if(dataTemp.get("tDestAddress") == ""){
                                destAddHeight = 0
                            }
                            
//                            let vTypeNameTxt = "\(dataTemp.get("SelectedCategory"))-\(dataTemp.get("SelectedVehicle"))"
                            let vTypeNameTxt = "\(dataTemp.get("vVehicleCategory")) - \(dataTemp.get("vVehicleType"))"
                            
                            var vTypeNameHeight = vTypeNameTxt.trim().height(withConstrainedWidth: Application.screenSize.width - 50, font: UIFont(name: "Roboto-Light", size: 16)!)
//                            - 20
                            if(vTypeNameHeight < 0 || dataTemp.get("eType") != Utils.cabGeneralType_UberX || (dataTemp.get("vVehicleCategory") == "" && dataTemp.get("vVehicleType") == "")){
                                vTypeNameHeight = 0
                                if(dataTemp.get("eType") != Utils.cabGeneralType_UberX ){
                                    vTypeNameHeight = -20
                                }
                            }
                            
                            if(dataTemp.get("eType") == Utils.cabGeneralType_UberX && dataTemp.get("eFareType") != "Regular" && (dataTemp.get("eStatus") == "Declined" || (dataTemp.get("eStatus") == "Cancel" && dataTemp.get("eCancelBy") == "Driver"))){
                                destAddHeight = destAddHeight + 35
                            }
                            
                            self.extraHeightContainer += [sourceAddHeight + destAddHeight + vTypeNameHeight]
                        }else{
                            let sourceAddHeight = dataTemp.get("tSaddress").height(withConstrainedWidth: Application.screenSize.width - 106, font: UIFont(name: "Roboto-Light", size: 16)!) - 20
                            var destAddHeight = dataTemp.get("tDaddress").height(withConstrainedWidth: Application.screenSize.width - 106, font: UIFont(name: "Roboto-Light", size: 16)!) - 20
                            
                            if(dataTemp.get("tDaddress") == ""){
                                destAddHeight = 0
                            }
                            
                            let vTypeNameTxt = "\(dataTemp.get("vVehicleCategory")) - \(dataTemp.get("vVehicleType"))"
                            
                            var vTypeNameHeight = vTypeNameTxt.trim().height(withConstrainedWidth: Application.screenSize.width - 46, font: UIFont(name: "Roboto-Light", size: 16)!) - 20
                            
                            if(vTypeNameHeight < 0 || dataTemp.get("eType") != Utils.cabGeneralType_UberX || (dataTemp.get("vVehicleCategory") == "" && dataTemp.get("vVehicleType") == "")){
                                vTypeNameHeight = 0
                            }
                            
                            self.extraHeightContainer += [sourceAddHeight + destAddHeight + vTypeNameHeight]
                        }
                        
                    }
                    let NextPage = dataDict.get("NextPage")
                    
                    if(NextPage != "" && NextPage != "0"){
                        self.isNextPageAvail = true
                        self.nextPage_str = Int(NextPage)!
                        
                        self.addFooterView()
                    }else{
                        self.isNextPageAvail = false
                        self.nextPage_str = 0
                        
                        self.removeFooterView()
                    }
                    
                    self.tableView.reloadData()
                    
                }else{
                    if(isLoadingMore == false){
                        if(self.msgLbl != nil){
                            self.msgLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message"))
                            self.msgLbl.isHidden = false
                        }else{
                            self.msgLbl = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                        }
                        
                    }else{
                        self.isNextPageAvail = false
                        self.nextPage_str = 0
                        
                        self.removeFooterView()
                    }
                    
                }
                
                //                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                
                
            }else{
                if(isLoadingMore == false){
                    self.generalFunc.setError(uv: self)

                }
            }
            
            self.isLoadingMore = false
            self.loaderView.isHidden = true
            
        })
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        // #warning Incomplete implementation, return the number of sections
        return 1
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        
        return self.dataArrList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        
        let item = self.dataArrList[indexPath.item]
        var vBookingNo = ""
        
        
            let cell = tableView.dequeueReusableCell(withIdentifier: "RideHistoryTVCell", for: indexPath) as! RideHistoryTVCell
            
            if(self.HISTORY_TYPE == "PAST"){
                cell.rideDateLbl.text = Utils.convertDateFormateInAppLocal(date: Utils.convertDateGregorianToAppLocale(date: item.get("tTripRequestDateOrig"), dateFormate: "yyyy-MM-dd HH:mm:ss"), toDateFormate: Utils.dateFormateWithTime)
                cell.pickUpLocVLbl.text = item.get("tSaddress")
                cell.destVLbl.text = item.get("tDaddress") == "" ? "----" : item.get("tDaddress")
                cell.cancelBtn.isHidden = true
                cell.statusView.isHidden = false
                vBookingNo = Configurations.convertNumToAppLocal(numStr: item.get("vRideNo"))
            }else{
                //            cell.rideDateLbl.text = item.get("dBooking_date")
                cell.rideDateLbl.text = Utils.convertDateFormateInAppLocal(date: Utils.convertDateGregorianToAppLocale(date: item.get("dBooking_dateOrig"), dateFormate: "yyyy-MM-dd HH:mm:ss"), toDateFormate: Utils.dateFormateWithTime)
                cell.pickUpLocVLbl.text = item.get("vSourceAddresss")
                cell.destVLbl.text = item.get("tDestAddress") == "" ? "----" : item.get("tDestAddress")
                cell.cancelBtn.isHidden = false
                cell.statusView.isHidden = true
                //            vBookingNo = item.get("vBookingNo")
                vBookingNo = Configurations.convertNumToAppLocal(numStr: item.get("vBookingNo"))
                
                if(item.get("eStatus") == "Pending"){
                    cell.cancelBtn.isHidden = false
                    cell.statusView.isHidden = true
                }else{
                    cell.cancelBtn.isHidden = true
                    cell.statusView.isHidden = false
                }
            }
            
            cell.destVLbl.fitText()
            cell.pickUpLocVLbl.fitText()
            
            cell.statusHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_Status") + ":"
            if(item.get("eCancelled") == "Yes"){
                cell.statusVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCELED_TXT")
            }else{
                if(item.get("iActive") == "Canceled"){
                    cell.statusVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCELED_TXT")
                }else if(item.get("iActive") == "Finished"){
                    cell.statusVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FINISHED_TXT")
                }else {
                    cell.statusVLbl.text = item.get("iActive") == "" ? item.get("eStatus") : item.get("iActive")
                }
            }
            
            cell.cancelBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: item.get("eType") == Utils.cabGeneralType_Deliver ? "LBL_CANCEL_DELIVERY" : "LBL_CANCEL_BOOKING"))
            cell.cancelBtn.tag = indexPath.item
            cell.cancelBtn.clickDelegate = self
            
            cell.bookingNoLbl.text = self.generalFunc.getLanguageLabel(origValue: item.get("eType") == Utils.cabGeneralType_Deliver ? "Delivery" : "Booking", key: item.get("eType") == Utils.cabGeneralType_Deliver ? "LBL_DELIVERY" : "LBL_BOOKING") + "# " + vBookingNo
            
//            cell.pickUpLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Pick up location", key: "LBL_PICK_UP_LOCATION")
            
            cell.pickUpLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: item.get("eType") == Utils.cabGeneralType_UberX ? "Job Location" : (item.get("eType") == Utils.cabGeneralType_Deliver ? "Sender's location" : "Pick up location"), key: item.get("eType") == Utils.cabGeneralType_UberX ? "LBL_JOB_LOCATION_TXT" : (item.get("eType") == Utils.cabGeneralType_Deliver ? "LBL_SENDER_LOCATION" : "LBL_PICK_UP_LOCATION"))
            
            
            cell.destHLbl.text = self.generalFunc.getLanguageLabel(origValue: item.get("eType") == Utils.cabGeneralType_Deliver ? "Receiver's location" : "Destination location", key: item.get("eType") == Utils.cabGeneralType_Deliver ? "LBL_RECEIVER_LOCATION" : "LBL_DEST_LOCATION")
            
//            cell.destHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Destination location", key: "LBL_DEST_LOCATION")
            
            cell.rideTypeLbl.text = item.get("eType") == Utils.cabGeneralType_Deliver ? self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DELIVERY") : (item.get("eType") == Utils.cabGeneralType_Ride ? self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RIDE") : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BOOKING"))
            
            if(self.APP_TYPE.uppercased() == "RIDE-DELIVERY"){
                cell.rideDateLbl.isHidden = false
            }else{
                cell.rideTypeLbl.text = cell.rideDateLbl.text
                cell.rideDateLbl.isHidden = true
            }
            
//            if(self.userProfileJson.get("APP_DESTINATION_MODE").uppercased() == "NONE"){
//                cell.destVLbl.text = "---"
//            }
            
            cell.dataView.layer.shadowOpacity = 0.5
            cell.dataView.layer.shadowOffset = CGSize(width: 0, height: 3)
            cell.dataView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
            
            
            //            cell.dashedView.addDashedBorder(strokeColor: UIColor(hex: 0xADADAD), lineWidth: 2)
            cell.dashedView.backgroundColor = UIColor.clear
            
            DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(0.5 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                
                cell.dashedView.addDashedLine(color: UIColor(hex: 0xADADAD), lineWidth: 2)
            })
            
            
            GeneralFunctions.setImgTintColor(imgView: cell.locPinImgView, color: UIColor(hex: 0xd73030))
            
            cell.selectionStyle = .none
            cell.backgroundColor = UIColor.clear
            return cell
        
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        if(self.HISTORY_TYPE == "PAST"){
            let rideDetailUV = GeneralFunctions.instantiateViewController(pageName: "RideDetailUV") as! RideDetailUV
            rideDetailUV.tripDetailDict = self.dataArrList[indexPath.item]
            self.pushToNavController(uv: rideDetailUV)
        }
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat
    {
        
        if(indexPath.item < self.extraHeightContainer.count){
//            return self.extraHeightContainer[indexPath.item] + 295
            let item = dataArrList[indexPath.item]
            
            if(item.get("tDaddress") == "" && item.get("tDestAddress") == ""){
                return self.extraHeightContainer[indexPath.item] + 250
            }else{
                return self.extraHeightContainer[indexPath.item] + 295
            }
        }
        
        return 295
    }
    
    func scrollViewDidScroll(_ scrollView: UIScrollView) {
        let currentOffset = scrollView.contentOffset.y;
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height;
        
        
        if (maximumOffset - currentOffset <= 15) {
            
            if(isNextPageAvail==true && isLoadingMore==false){
                
                isLoadingMore=true
                
                getDtata(isLoadingMore: isLoadingMore)
            }
        }
    }
    
    func addFooterView(){
        let loaderView =  self.generalFunc.addMDloader(contentView: self.tableView, isAddToParent: false)
        loaderView.backgroundColor = UIColor.clear
        loaderView.frame = CGRect(x:0, y:0, width: Application.screenSize.width, height: 80)
        self.tableView.tableFooterView  = loaderView
        self.tableView.tableFooterView?.isHidden = false
    }
    
    func removeFooterView(){
        self.tableView.tableFooterView = UIView(frame: CGRect.zero)
        self.tableView.tableFooterView?.isHidden = true
    }
    
    func myBtnTapped(sender: MyButton) {
        
        if(sender.btnType == "RE_BOOKING"){
            
            let item = self.dataArrList[sender.tag]
            
            var customDataDict = [String:String]()
            
            customDataDict["iVehicleCategoryId"] = item.get("SelectedCategoryId")
            customDataDict["vCategory"] = item.get("SelectedCategory")
            customDataDict["ePriceType"] = item.get("SelectedPriceType")
            customDataDict["vVehicleType"] = item.get("SelectedVehicle")
            customDataDict["eFareType"] = item.get("SelectedFareType")
            customDataDict["fFixedFare"] = "\(item.get("SelectedCurrencySymbol"))\(item.get("SelectedPrice"))"
            customDataDict["fPricePerHour"] = "\(item.get("SelectedCurrencySymbol"))\(item.get("SelectedPrice"))"
            customDataDict["fPricePerKM"] = "\(item.get("SelectedCurrencySymbol"))\(item.get("SelectedPricePerKM"))"
            customDataDict["fPricePerMin"] = "\(item.get("SelectedCurrencySymbol"))\(item.get("SelectedPricePerMin"))"
            customDataDict["iBaseFare"] = "\(item.get("SelectedCurrencySymbol"))\(item.get("SelectedBaseFare"))"
            customDataDict["fCommision"] = "\(item.get("SelectedCurrencySymbol"))\(item.get("SelectedCommision"))"
            customDataDict["iMinFare"] = "\(item.get("SelectedCurrencySymbol"))\(item.get("SelectedMinFare"))"
            customDataDict["iPersonSize"] = "\(item.get("SelectedCurrencySymbol"))\(item.get("SelectedPersonSize"))"
            customDataDict["vVehicleTypeImage"] = item.get("SelectedVehicleTypeImage")
            customDataDict["eType"] = item.get("SelectedeType")
            customDataDict["eIconType"] = item.get("SelectedeIconType")
            customDataDict["eAllowQty"] = item.get("SelectedAllowQty")
            customDataDict["iMaxQty"] = item.get("SelectediMaxQty")
            customDataDict["iVehicleTypeId"] = item.get("iVehicleTypeId")
            customDataDict["fFixedFare_value"] = item.get("SelectedPrice")
            customDataDict["fPricePerHour_value"] = item.get("SelectedPrice")
            customDataDict["ALLOW_SERVICE_PROVIDER_AMOUNT"] = item.get("ALLOW_SERVICE_PROVIDER_AMOUNT")
            customDataDict["vCategoryTitle"] = item.get("SelectedCategoryTitle")
            customDataDict["vCategoryDesc"] = item.get("SelectedCategoryDesc")
            customDataDict["vSymbol"] = item.get("SelectedCurrencySymbol")
            
            let ufxServiceItemDict = customDataDict as NSDictionary
                        
            let chooseServiceDateUv = GeneralFunctions.instantiateViewController(pageName: "ChooseServiceDateUV") as! ChooseServiceDateUV
            chooseServiceDateUv.ufxSelectedVehicleTypeId = item.get("iVehicleTypeId")
            chooseServiceDateUv.ufxSelectedVehicleTypeName = item.get("SelectedVehicle")
            chooseServiceDateUv.ufxSelectedQty = item.get("SelectedQty")
            chooseServiceDateUv.ufxAddressId = item.get("iUserAddressId")
            chooseServiceDateUv.ufxSelectedLatitude = item.get("vSourceLatitude")
            chooseServiceDateUv.ufxSelectedLongitude = item.get("vSourceLongitude")
            chooseServiceDateUv.serviceAreaAddress = item.get("vSourceAddresss")
            chooseServiceDateUv.ufxCabBookingId = item.get("iCabBookingId")
            chooseServiceDateUv.ufxServiceItemDict = ufxServiceItemDict
            self.pushToNavController(uv: chooseServiceDateUv)
            
            return
        }
        
        cancelBooking(position: sender.tag)
    }
    
    func cancelBooking(position:Int){
        
        let bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        bgView.frame = self.contentView.frame
        
        bgView.center = CGPoint(x: self.view.bounds.midX, y: self.view.bounds.midY)
        
        let cancelBookingView = CancelBookingView(frame: CGRect(x: self.view.frame.width / 2, y: self.view.frame.height / 2, width: (Application.screenSize.width > 390 ? 375 : (Application.screenSize.width - 50)), height: 200))
        cancelBookingView.center = CGPoint(x: self.view.bounds.midX, y: self.view.bounds.midY)
        cancelBookingView.setViewHandler { (isViewClose, view, isPositiveBtnClicked, reason) in
            
            cancelBookingView.frame.origin.y = Application.screenSize.height + 1000
            
            bgView.removeFromSuperview()
            
            self.view.layoutIfNeeded()
            
            if(isPositiveBtnClicked){
                self.continueCancelBooking(iCabBookingId: self.dataArrList[position].get("iCabBookingId"), reason: reason)
            }
        }
        
        Utils.createRoundedView(view: cancelBookingView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        cancelBookingView.layer.shadowOpacity = 0.5
        cancelBookingView.layer.shadowOffset = CGSize(width: 0, height: 3)
        cancelBookingView.layer.shadowColor = UIColor.black.cgColor
        
        self.view.addSubview(bgView)
        self.view.addSubview(cancelBookingView)
        
    }
    
    func continueCancelBooking(iCabBookingId: String, reason:String){
        let parameters = ["type":"cancelBooking", "iUserId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "iCabBookingId": iCabBookingId, "Reason": reason]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BOOKING_CANCELED"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                        self.isLoadingMore = false
                        self.dataArrList.removeAll()
                        self.tableView.reloadData()
                        self.nextPage_str = 1
                        self.getDtata(isLoadingMore: false)
                    })
                    
                    
                    
                }else{
                    if(dataDict.get(Utils.message_str) == "DO_RESTART"){
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                        return
                    }
                    
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }

    @IBAction func unwindToRideHistoryScreen(_ segue:UIStoryboardSegue) {
        
        if(segue.source.isKind(of: RideDetailUV.self))
        {
            if(self.HISTORY_TYPE == "PAST"){
                let iTripId = (segue.source as! RideDetailUV).tripDetailDict.get("iTripId")
                var dataList = [NSDictionary]()
                dataList.append(contentsOf: dataArrList)
                
                self.dataArrList.removeAll()
                
                for i in 0..<dataList.count{
                    
                    let item = dataList[i]
                    let tripId = item.get("iTripId")
                    
                    if(iTripId == tripId){
                        item.setValue("Yes", forKey: "is_rating")
                    }
                    
                    self.dataArrList += [item]
                }
                
                self.tableView.reloadData()
            }
           
        }else if(segue.source.isKind(of: MainScreenUV.self)){
            // Called when booking is successfully finished
            
            let mainScreenUv = segue.source as! MainScreenUV
            
            if(mainScreenUv.ufxCabBookingId != ""){
                self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Your selected booking has been updated.", key: "LBL_BOOKING_UPDATED"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                    self.isLoadingMore = false
                    self.dataArrList.removeAll()
                    self.tableView.reloadData()
                    self.nextPage_str = 1
                    
                    if(self.msgLbl != nil){
                        self.msgLbl.isHidden = true
                    }
                    
                    self.getDtata(isLoadingMore: false)
                })
            }
        }
        
    }
    
}

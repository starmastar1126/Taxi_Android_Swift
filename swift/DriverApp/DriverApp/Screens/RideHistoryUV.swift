//
//  RideHistroyUV.swift
//  DriverApp
//
//  Created by NEW MAC on 13/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class RideHistoryUV: UIViewController, UITableViewDataSource, UITableViewDelegate, MyBtnClickDelegate, FSCalendarDelegate, FSCalendarDataSource {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    
    // Date OutLets
    @IBOutlet weak var calendarHeightConstraint: NSLayoutConstraint!
    @IBOutlet weak var calendarView: FSCalendar!
    @IBOutlet weak var leftArrowImgView: UIImageView!
    @IBOutlet weak var rightArrowImgView: UIImageView!
    @IBOutlet weak var monthLabel: MyLabel!
    
    @IBOutlet weak var monthHeaderView: UIView!
    
    let generalFunc = GeneralFunctions()
    
    var HISTORY_TYPE = "PAST"
    
    var userProfileJson:NSDictionary!
    
    var loaderView:UIView!
    
    var dataArrList = [NSDictionary]()
    var nextPage_str = 1
    var isLoadingMore = false
    var isNextPageAvail = false
    
    var APP_TYPE = ""
    
    let formatter = DateFormatter()
    
    var testCalendar = Calendar(identifier: Configurations.getCalendarIdentifire())
    
    var registrationDate = ""
    
    var startDate:Date!
    
    var cntView:UIView!
    
    var extraHeightContainer = [CGFloat]()
    
    var currentPageDate:Date!
    
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
//        if(HISTORY_TYPE != "PAST" && loaderView != nil && self.isFirstCallFinished == true){
        if(HISTORY_TYPE != "PAST" && loaderView != nil && self.isDataLoaded == true){
            self.dataArrList.removeAll()
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
    
    override func viewDidAppear(_ animated: Bool) {
        
        if(isDataLoaded == false){
            
//            DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//                self.cntView.frame = self.view.frame
//                self.cntView.frame.size = CGSize(width: Application.screenSize.width, height: self.view.frame.height)
//                self.cntView.setNeedsLayout()
//
//
//            })

            if(self.HISTORY_TYPE != "PAST"){
                self.dataArrList.removeAll()
                self.getDtata(isLoadingMore: self.isLoadingMore)
            }
            
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
    
//    override func viewDidAppear(_ animated: Bool) {
//        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//            self.cntView.frame = self.view.frame
//            self.cntView.frame.size = CGSize(width: Application.screenSize.width, height: self.view.frame.height)
//            self.cntView.setNeedsLayout()
//        })
//    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        APP_TYPE = userProfileJson.get("APP_TYPE")
        
        if(HISTORY_TYPE != "PAST"){
            cntView = self.generalFunc.loadView(nibName: "RideHistoryScreenDesign", uv: self, contentView: contentView)
            
            self.contentView.addSubview(cntView)
            
//            self.contentView.addSubview(self.generalFunc.loadView(nibName: "RideHistoryScreenDesign", uv: self, contentView: contentView))
            
            self.tableView.delegate = self
            
            self.tableView.bounces = false
            self.tableView.dataSource = self
            self.tableView.tableFooterView = UIView()
            self.tableView.contentInset = UIEdgeInsetsMake(6, 0, GeneralFunctions.getSafeAreaInsets().bottom + 6, 0)
            
            self.tableView.register(UINib(nibName: "RideHistoryUFXListTVCell", bundle: nil), forCellReuseIdentifier: "RideHistoryUFXListTVCell")
            self.tableView.register(UINib(nibName: "RideHistoryListTVCell", bundle: nil), forCellReuseIdentifier: "RideHistoryTVCell")
            
            self.dataArrList.removeAll()
//            getDtata(isLoadingMore: self.isLoadingMore)
        }else{
            
            
            formatter.dateFormat = "yyyy-MM-dd"
            formatter.locale = Locale(identifier: Configurations.getGoogleMapLngCode())
            
            registrationDate = userProfileJson.get("RegistrationDate")
            let convertedRegistrationDate = Utils.convertDateGregorianToAppLocale(date: registrationDate, dateFormate: "yyyy-MM-dd")
            
            startDate = convertedRegistrationDate

            cntView = self.generalFunc.loadView(nibName: "RideHistoryDateScreenDesign", uv: self, contentView: contentView)
            
            self.contentView.addSubview(cntView)
            
            
            if(Configurations.isRTLMode()){
//                calendarView.transform = CGAffineTransform(scaleX: -1, y: 1)
            }

            GeneralFunctions.setImgTintColor(imgView: leftArrowImgView, color: UIColor.UCAColor.AppThemeColor)
            GeneralFunctions.setImgTintColor(imgView: rightArrowImgView, color: UIColor.UCAColor.AppThemeColor)

            
            //        testCalendar.timeZone = NSTimeZone(abbreviation: "GMT")!
            testCalendar.locale = Locale(identifier: Configurations.getGoogleMapLngCode())
//            Locale(identifier: Configurations.getGoogleMapLngCode())
            
            self.calendarView.locale = Locale(identifier: Configurations.getGoogleMapLngCode())
            calendarView.dataSource = self
            calendarView.delegate = self
            calendarView.delegate = self
            calendarView.appearance.headerTitleColor = UIColor.UCAColor.AppThemeColor
            calendarView.appearance.weekdayTextColor = UIColor.UCAColor.AppThemeColor_1
//            calendarView.appearance.selectionColor = UIColor.clear
            calendarView.headerHeight = 55
            calendarView.appearance.headerBackgroundColor =  UIColor.UCAColor.AppThemeColor_1
            calendarView.appearance.todayColor = UIColor.UCAColor.AppThemeColor
            calendarView.appearance.borderRadius = 0
            self.calendarView.appearance.headerMinimumDissolvedAlpha = 0.0
            //        calendar.
            calendarView.reloadData()
            calendarView.clipsToBounds = true
            
            calendarView.backgroundColor = UIColor.clear
            
            calendarView.reloadData()
            
//            calendarView.scrollToDate(getCurrentDate())
            
            let previousMonthTapGue = UITapGestureRecognizer()
            let nextMonthTapGue = UITapGestureRecognizer()
            
            previousMonthTapGue.addTarget(self, action: #selector(self.previousMonthImgTapped))
            nextMonthTapGue.addTarget(self, action: #selector(self.nextMonthImgTapped))
            
            leftArrowImgView.isUserInteractionEnabled = true
            rightArrowImgView.isUserInteractionEnabled = true
            
            if(Configurations.isRTLMode()){
                leftArrowImgView.addGestureRecognizer(nextMonthTapGue)
                rightArrowImgView.addGestureRecognizer(previousMonthTapGue)
                
                
                rightArrowImgView.transform = CGAffineTransform(scaleX: -1, y: 1)
                
            }else{
                leftArrowImgView.addGestureRecognizer(previousMonthTapGue)
                rightArrowImgView.addGestureRecognizer(nextMonthTapGue)
                
                leftArrowImgView.transform = CGAffineTransform(scaleX: -1, y: 1)
            }
            
        }

        
        if(isDirectPush){
            self.addBackBarBtn()
            
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Your trips", key: "LBL_YOUR_TRIPS")
            self.title = self.generalFunc.getLanguageLabel(origValue: "Your trips", key: "LBL_YOUR_TRIPS")
        }
    }
    
    fileprivate let gregorianFormatter: DateFormatter = {
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy/MM/dd"
        formatter.locale = Locale(identifier: "en-US")
        formatter.calendar = Calendar(identifier: .gregorian)
        return formatter
    }()
    
    func calendarCurrentPageDidChange(_ calendar: FSCalendar) {
        let dateString = gregorianFormatter.string(from: calendar.currentPage)

        let date = Utils.convertStringToDate(dateStr: dateString, dateFormat: "yyyy-MM-dd")
        currentPageDate = date
    }
    
    func previousMonthImgTapped(){
        
        let cal = Calendar(identifier: .gregorian)
        let date = cal.date(byAdding: .month, value: -1, to: currentPageDate)
        
        calendarView.setCurrentPage(date!, animated: true)
    }
    
    func nextMonthImgTapped(){
        let cal = Calendar(identifier: .gregorian)
        let date = cal.date(byAdding: .month, value: 1, to: currentPageDate)
        
        calendarView.setCurrentPage(date!, animated: true)
    }
    
    func calendar(_ calendar: FSCalendar, didSelect date: Date, at monthPosition: FSCalendarMonthPosition) {
        
        let dateString = gregorianFormatter.string(from: date)
        
        let selectedDayRideHistoryUV = GeneralFunctions.instantiateViewController(pageName: "SelectedDayRideHistoryUV") as! SelectedDayRideHistoryUV
        selectedDayRideHistoryUV.selectedDate = dateString
        self.pushToNavController(uv: selectedDayRideHistoryUV)

        if monthPosition == .previous || monthPosition == .next {
            calendar.setCurrentPage(date, animated: true)
        }
    }
    
    func minimumDate(for calendar: FSCalendar) -> Date {
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        let registrationDate = userProfileJson.get("RegistrationDate")
        
        return Utils.convertStringToDate(dateStr: registrationDate, dateFormat: "yyyy-MM-dd")
    }
    
    func maximumDate(for calendar: FSCalendar) -> Date {
        let currentDate = Utils.getCurrentDateInGrogrian(dateFormat: "yyyy-MM-dd", timeZone: "GMT")
        self.currentPageDate = currentDate
        
        return currentDate
    }
    
    
    
    func calendar(_ calendar: FSCalendar, boundingRectWillChange bounds: CGRect, animated: Bool) {
        self.calendarHeightConstraint.constant = bounds.height
        self.view.layoutIfNeeded()
    }
    
    
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func getDtata(isLoadingMore:Bool){
        if(nextPage_str < 1){
            nextPage_str = 1
        }
        
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.view)
            loaderView.backgroundColor = UIColor.clear
        }else if(loaderView != nil && isLoadingMore == false){
            loaderView.isHidden = false
        }
        
        let parameters = ["type": HISTORY_TYPE == "PAST" ? "getRideHistory" : "checkBookings", "UserType": Utils.appUserType, "iDriverId": GeneralFunctions.getMemberd(), "page": self.nextPage_str.description, "DataType": self.HISTORY_TYPE]
        
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
                        let sourceAddHeight = dataTemp.get("vSourceAddresss").height(withConstrainedWidth: Application.screenSize.width - 106, font: UIFont(name: "Roboto-Light", size: 16)!)
                        
                        if(dataTemp.get("tDaddress") == "" && dataTemp.get("tDestAddress") == ""){
                            
                            let vTypeNameTxt = "\(dataTemp.get("SelectedCategory"))-\(dataTemp.get("SelectedVehicle"))"
                            
                            var vTypeNameHeight = vTypeNameTxt.height(withConstrainedWidth: Application.screenSize.width - 46, font: UIFont(name: "Roboto-Light", size: 16)!) - 20
                            
                            if(vTypeNameHeight < 0){
                                vTypeNameHeight = 0
                            }
                            
                            self.extraHeightContainer += [vTypeNameHeight + sourceAddHeight - 20]
                        }else{
                            let destAddHeight = dataTemp.get(self.HISTORY_TYPE != "PAST" ? "tDestAddress" : "tDaddress").height(withConstrainedWidth: Application.screenSize.width - 106, font: UIFont(name: "Roboto-Light", size: 16)!)
                            self.extraHeightContainer += [sourceAddHeight + destAddHeight - 20]
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

            cell.rideDateLbl.text = Utils.convertDateFormateInAppLocal(date: Utils.convertDateGregorianToAppLocale(date: item.get(self.HISTORY_TYPE == "PAST" ? "tTripRequestDateOrig" : "dBooking_dateOrig"), dateFormate: "yyyy-MM-dd HH:mm:ss"), toDateFormate: Utils.dateFormateWithTime)
            
            cell.pickUpLocVLbl.text = item.get(self.HISTORY_TYPE == "PAST" ? "tSaddress" : "vSourceAddresss")
            cell.destVLbl.text = item.get(self.HISTORY_TYPE == "PAST" ? "tDaddress" : "tDestAddress") == "" ? "----" : item.get(self.HISTORY_TYPE == "PAST" ? "tDaddress" : "tDestAddress")
            
            vBookingNo = Configurations.convertNumToAppLocal(numStr: item.get(self.HISTORY_TYPE == "PAST" ? "vRideNo" : "vBookingNo"))
            
            cell.cancelBtn.isHidden = self.HISTORY_TYPE == "PAST" ? true : false
            cell.startTripBtn.isHidden = self.HISTORY_TYPE == "PAST" ? true : false
            cell.statusView.isHidden = self.HISTORY_TYPE == "PAST" ? false : true
            
            cell.statusHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_Status") + ":"
            if(item.get("eCancelled") == "Yes"){
                cell.statusVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCELED_TXT")
            }else{
                if(item.get("iActive") == "Canceled"){
                    cell.statusVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCELED_TXT")
                }else if(item.get("iActive") == "Finished"){
                    cell.statusVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FINISHED_TXT")
                }else {
                    cell.statusVLbl.text = item.get("iActive")
                }
            }
            cell.destVLbl.fitText()
            cell.pickUpLocVLbl.fitText()
            
            cell.startTripBtn.btnType = "START"
            cell.cancelBtn.btnType = "CANCEL"
            
            cell.startTripBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: item.get("eType") == Utils.cabGeneralType_Deliver ? "LBL_ACCEPT_DELIVERY" : "LBL_BEGIN_TRIP"))
            
            cell.cancelBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: item.get("eType") == Utils.cabGeneralType_Deliver ? "LBL_CANCEL_DELIVERY" : "LBL_CANCEL_TRIP"))
            
            cell.cancelBtn.clickDelegate = self
            cell.startTripBtn.clickDelegate = self
            
            cell.cancelBtn.tag = indexPath.item
            cell.startTripBtn.tag = indexPath.item
            
            cell.bookingNoLbl.text = self.generalFunc.getLanguageLabel(origValue: item.get("eType") == Utils.cabGeneralType_Deliver ? "Delivery" : "Booking", key: item.get("eType") == Utils.cabGeneralType_Deliver ? "LBL_DELIVERY" : "LBL_BOOKING") + "# " + vBookingNo
            
            cell.pickUpLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Pick up location", key: "LBL_PICK_UP_LOCATION")
            cell.destHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Destination location", key: "LBL_DEST_LOCATION")
            cell.rideTypeLbl.text = item.get("eType") == Utils.cabGeneralType_Deliver ? self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DELIVERY") : (item.get("eType") == Utils.cabGeneralType_Ride ? self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RIDE") : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BOOKING"))
            
            
            if(self.APP_TYPE.uppercased() == "RIDE-DELIVERY"){
                cell.rideDateLbl.isHidden = false
            }else{
                cell.rideTypeLbl.text = cell.rideDateLbl.text
                cell.rideDateLbl.isHidden = true
            }
            
            if(self.userProfileJson.get("APP_DESTINATION_MODE").uppercased() == "NONE"){
                cell.destVLbl.text = "---"
            }
            
            cell.dataView.layer.shadowOpacity = 0.5
            cell.dataView.layer.shadowOffset = CGSize(width: 0, height: 3)
            cell.dataView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
            
            //        cell.dashedView.addDashedLine(color: UIColor(hex: 0xADADAD), lineWidth: 2)
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
        
//        if(self.HISTORY_TYPE == "PAST"){
//            let rideDetailUV = GeneralFunctions.instantiateViewController(pageName: "RideDetailUV") as! RideDetailUV
//            rideDetailUV.tripDetailDict = self.dataArrList[indexPath.item]
//            self.pushToNavController(uv: rideDetailUV)
//        }
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat
    {
        
        if(indexPath.item < self.extraHeightContainer.count){
             let item = dataArrList[indexPath.item]
            
            if(item.get("tDaddress") == "" && item.get("tDestAddress") == ""){
                return self.extraHeightContainer[indexPath.item] + 245
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
        if(sender.btnType == "CANCEL"){
            cancelBooking(position: sender.tag, bookingType: self.dataArrList[sender.tag].get("eType"))
        }else if(sender.btnType == "START"){
            
            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONFIRM_START_TRIP_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedIndex) in
                
                if(btnClickedIndex == 0){
                    self.generateTrip(position: sender.tag)
                }
            })
        }else if(sender.btnType == "ACCEPT_JOB"){
            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Are you sure, you want to accept this job?", key: "LBL_CONFIRM_ACCEPT_JOB"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedIndex) in
                
                if(btnClickedIndex == 0){
                    self.updateBookingStatus(status: "Accepted", iCabBookingId: self.dataArrList[sender.tag].get("iCabBookingId"), vCancelReason: "", position: sender.tag, eConfirmByProvider: "No")
                }
            })
            
        }else if(sender.btnType == "DELINE_JOB"){
            updateBookingStatus(status: "Declined", iCabBookingId: self.dataArrList[sender.tag].get("iCabBookingId"), vCancelReason: "", position: sender.tag, eConfirmByProvider: "No")
        }
    }
    
    func updateBookingStatus(status:String, iCabBookingId:String, vCancelReason:String, position:Int, eConfirmByProvider:String){
        
        if(status == "Declined" && vCancelReason == ""){
            cancelBooking(position: position, bookingType: self.dataArrList[position].get("eType"))
            return
        }
        
        let parameters = ["type":"UpdateBookingStatus", "iDriverId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "eStatus": status, "iCabBookingId": iCabBookingId, "vCancelReason": vCancelReason, "eConfirmByProvider": eConfirmByProvider]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                        
                        self.dataArrList.removeAll()
                        self.isLoadingMore = false
                        self.nextPage_str = 1
                        self.isNextPageAvail = false
                        self.tableView.reloadData()
                        if(self.msgLbl != nil){
                            self.msgLbl.isHidden = true
                        }
                        self.getDtata(isLoadingMore: false)
                        
                    })
                    
                }else{
                    
                    if (dataDict.get("Action") == "0" && dataDict.get("BookingFound").uppercased() == "YES")
                    {
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "We found that you have booking with same time slot. Are you sure you want to accept?", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YES"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO"), completionHandler: { (btnClickedId) in
                            
                            if(btnClickedId == 0){
                                self.updateBookingStatus(status: status, iCabBookingId: iCabBookingId, vCancelReason: vCancelReason, position: position, eConfirmByProvider: "Yes")
                            }
                            
                        })
                        
                    }
                    else
                    {
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }
                    
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func generateTrip(position:Int){
        let parameters = ["type":"GenerateTrip", "DriverID": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "iCabBookingId": self.dataArrList[position].get("iCabBookingId"), "GoogleServerKey": Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY")]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                    
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
    
    func cancelBooking(position:Int, bookingType:String){
        
        let bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        bgView.frame = self.contentView.frame
        
        bgView.center = CGPoint(x: self.view.bounds.midX, y: self.view.bounds.midY)
        
        let cancelBookingView = CancelBookingView(frame: CGRect(x: self.view.frame.width / 2, y: self.view.frame.height / 2, width: (Application.screenSize.width > 390 ? 375 : (Application.screenSize.width - 50)), height: 200))
        cancelBookingView.center = CGPoint(x: self.view.bounds.midX, y: self.view.bounds.midY)
        cancelBookingView.bookingType = bookingType
        cancelBookingView.PAGE_TYPE = self.HISTORY_TYPE
        cancelBookingView.setLabels()
        cancelBookingView.setViewHandler { (isViewClose, view, isPositiveBtnClicked, reason) in
            
            cancelBookingView.frame.origin.y = Application.screenSize.height + 1000
            
            bgView.removeFromSuperview()
            
            self.view.layoutIfNeeded()
            
            if(isPositiveBtnClicked){
                if(bookingType == Utils.cabGeneralType_UberX){
                    self.updateBookingStatus(status: "Declined", iCabBookingId: self.dataArrList[position].get("iCabBookingId"), vCancelReason: reason, position: position, eConfirmByProvider: "No")
                }else{
                    self.continueCancelBooking(iCabBookingId: self.dataArrList[position].get("iCabBookingId"), reason: reason)
                }
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
                        self.getDtata(isLoadingMore: self.isLoadingMore)
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
    
    

}

//
//  ChooseServiceDateUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 06/10/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ChooseServiceDateUV: UIViewController, UICollectionViewDelegate, UICollectionViewDataSource, MyBtnClickDelegate  {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var serviceLocHLbl: MyLabel!
    @IBOutlet weak var serviceLocVLbl: MyLabel!
    @IBOutlet weak var chooseDateHLbl: MyLabel!
    @IBOutlet weak var collectionView: UICollectionView!
    @IBOutlet weak var nextBtn: MyButton!
    @IBOutlet weak var timeCollectionView: UICollectionView!
    @IBOutlet weak var chooseDateHLblTopMargin: NSLayoutConstraint!
    
    var isFromMainScreen = false
    
    var bookingType = ""
    
    var ufxSelectedVehicleTypeId = ""
    var ufxSelectedVehicleTypeName = ""
    var ufxSelectedLatitude = ""
    var ufxSelectedLongitude = ""
    var ufxSelectedAddress = ""
    var ufxSelectedQty = ""
    var ufxAddressId = ""
    var ufxCabBookingId = ""
    var serviceAreaAddress = ""
    var ufxServiceItemDict:NSDictionary!
    
    var isDirectOpenFromUFXAddress = false
    
    let formatter = DateFormatter()
    
    var testCalendar = Calendar(identifier: Configurations.getCalendarIdentifire())
    
    let generalFunc = GeneralFunctions()
    var registrationDate = ""
    
    var userProfileJson:NSDictionary!
    var selectedDate:Date!
    
    var datesArr = [Date]()
    
    var timeArr = [String]()
    var timeAMPMArr = [String]()
    var time24Arr = [String]()
    var selectedTime = ""
    
    var finalDate = ""
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
        GeneralFunctions.postNotificationSignal(key: ConfigPubNub.resumeInst_key, obj: self)
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "ChooseServiceDateScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        setData()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func setData(){
        
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Choose Booking Date", key: "LBL_CHOOSE_BOOKING_DATE")
        self.title = self.generalFunc.getLanguageLabel(origValue: "Choose Booking Date", key: "LBL_CHOOSE_BOOKING_DATE")
        
        self.serviceLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Service Address", key: "LBL_SERVICE_ADDRESS_HINT_INFO")
        self.serviceLocVLbl.text = self.serviceAreaAddress
        self.serviceLocVLbl.fitText()
        
        if(isFromMainScreen == true){
            self.serviceLocHLbl.isHidden = true
            self.serviceLocVLbl.isHidden = true
            self.chooseDateHLblTopMargin.constant = -50
        }
        
        self.chooseDateHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BOOKING_DATE")
        
        self.chooseDateHLbl.backgroundColor = UIColor.UCAColor.AppThemeColor_1
        self.chooseDateHLbl.textColor = UIColor.UCAColor.AppThemeColor
        
        self.nextBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTINUE_BTN"))
        self.nextBtn.clickDelegate = self
        formatter.dateFormat = "yyyy-MM-dd"
        formatter.locale = Locale(identifier: Configurations.getGoogleMapLngCode())
        
        testCalendar.locale = Locale(identifier: Configurations.getGoogleMapLngCode())
        
        
        datesArr = getDatesArr(calendar: Calendar(identifier: Configurations.getCalendarIdentifire()))
        self.selectedDate = datesArr[0]
        self.collectionView.register(UINib(nibName: "JobDateSelectionCVCell", bundle: nil), forCellWithReuseIdentifier: "JobDateSelectionCVCell")
        self.collectionView.dataSource = self
        self.collectionView.delegate = self
        self.collectionView.reloadData()
        
        
        for i in 0..<24 {
            
            var fromTime = i
            var toTime = i + 1
            
            if(fromTime < 12){
                self.timeAMPMArr += ["\(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_AM_TXT"))"]
            }else{
                self.timeAMPMArr += ["\(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PM_TXT"))"]
            }
            
            time24Arr += ["\(fromTime < 10 ? "\(fromTime < 1 ? "12" : "0\(fromTime)")" : "\(fromTime)")" + "-" + "\(toTime < 10 ? "0\(toTime)" : "\(toTime)")"]
            
            fromTime = fromTime % 12
            toTime = toTime % 12
            if(fromTime == 0){
                fromTime = 12
            }
            
            if(toTime == 0){
                toTime = 12
            }
            
            timeArr += ["\(Configurations.convertNumToAppLocal(numStr: "\(fromTime < 10 ? "\(fromTime < 1 ? "12" : "0\(fromTime)")" : "\(fromTime)")")) - \(Configurations.convertNumToAppLocal(numStr: "\(toTime < 10 ? "0\(toTime)" : "\(toTime)")"))"]
        }
        
        self.timeCollectionView.register(UINib(nibName: "JobTimeSelectionCVCell", bundle: nil), forCellWithReuseIdentifier: "JobTimeSelectionCVCell")
        self.timeCollectionView.dataSource = self
        self.timeCollectionView.delegate = self
        
        let layout = KTCenterFlowLayout()
        layout.minimumInteritemSpacing = 10.0
        layout.minimumLineSpacing = 10.0
        layout.itemSize = CGSize(width: 105, height: 45)
        self.timeCollectionView.setCollectionViewLayout(layout, animated: true)
        
        self.timeCollectionView.reloadData()
        
    }
    
    func getIndexOfSelectedTime() -> Int{
        for i in 0..<self.timeArr.count{
            if(self.selectedTime == self.time24Arr[i].trimAll()){
                return i
            }
        }
        return 0
    }
    
    
    override func closeCurrentScreen() {
         
        super.closeCurrentScreen()
    }
    
    func getDatesArr(calendar:Calendar) -> [Date]{
        var datesArr = [Date]()

        var currentDate = Date()
        let maxDate = calendar.date(byAdding: .month, value: Utils.MAX_DATE_SELECTION_MONTH_FROM_CURRENT, to: currentDate)
        
        while currentDate < maxDate! {
            datesArr += [currentDate]
            currentDate = currentDate.addedBy(days: 1)
        }
        
        return datesArr
    }
    
    fileprivate let gregorianFormatter: DateFormatter = {
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy/MM/dd"
        formatter.locale = Locale(identifier: "en-US")
        formatter.calendar = Calendar(identifier: .gregorian)
        return formatter
    }()
    
    func numberOfSections(in collectionView: UICollectionView) -> Int {
        return 1
    }
    
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        if(collectionView == self.timeCollectionView){
            return self.timeArr.count
        }
        return datesArr.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        if(collectionView == self.timeCollectionView){
            let cell = collectionView.dequeueReusableCell(withReuseIdentifier: "JobTimeSelectionCVCell", for: indexPath) as! JobTimeSelectionCVCell
            let item = self.timeArr[indexPath.item]
            cell.timeLbl.text = item + "  \(self.timeAMPMArr[indexPath.item])"
            if(self.selectedTime == self.time24Arr[indexPath.item].trimAll()){
                cell.timeLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
                cell.timeLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
            }else{
                cell.timeLbl.textColor = UIColor(hex: 0x1c1c1c)
                cell.timeLbl.backgroundColor = UIColor.clear
            }
            cell.timeLbl.layer.masksToBounds = true
            return cell
        }
        
        let cell = collectionView.dequeueReusableCell(withReuseIdentifier: "JobDateSelectionCVCell", for: indexPath) as! JobDateSelectionCVCell
        
        let date = self.datesArr[indexPath.item]
        let weekDay = Utils.convertDateFormateInAppLocal(date: date, toDateFormate: "EEE")
        cell.dayNameLbl.text = weekDay
        
        let dayNum = Utils.convertDateFormateInAppLocal(date: date, toDateFormate: "d")
        cell.dayNumLbl.text = dayNum
        
        Utils.createRoundedView(view: cell.dayNumLbl, borderColor: UIColor.clear, borderWidth: 0)
        
        if(selectedDate == date){
            cell.dayNumLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
            cell.dayNumLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
            
            self.chooseDateHLbl.text = Utils.convertDateFormateInAppLocal(date: date, toDateFormate: "MMMM yyyy")
        }else{
            cell.dayNumLbl.textColor = UIColor(hex: 0x1c1c1c)
            cell.dayNumLbl.backgroundColor = UIColor.clear
        }
        
        return cell
    }
    
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        if(collectionView == self.timeCollectionView){
            let previousItemIndex = self.getIndexOfSelectedTime()
//            self.selectedTime = self.timeArr[indexPath.item]
            self.selectedTime = self.time24Arr[indexPath.item].trimAll()
        
//            self.timeCollectionView.reloadItems(at: [IndexPath(row: indexPath.item, section: indexPath.section), IndexPath(row: previousItemIndex, section: 0)])
            self.timeCollectionView.reloadData()
//            self.collectionView.reloadData()
            return
        }
        
        self.selectedDate = self.datesArr[indexPath.item]
        
        self.collectionView.reloadData()
        self.collectionView.scrollToItem(at: IndexPath(row: indexPath.item, section: indexPath.section), at: .centeredHorizontally, animated: true)
        
    }
    
    func scrollViewDidEndDecelerating(_ scrollView: UIScrollView) {
        if(scrollView == self.timeCollectionView){
            
            return
        }
        self.fitToCenter()
    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        if(scrollView == self.timeCollectionView){
            
            return
        }
        self.fitToCenter()
    }
    
    func fitToCenter(){
        let collectionOrigin = self.collectionView.bounds.origin
        let collectionWidth = self.collectionView.bounds.width
        var centerPoint: CGPoint!
        var newX: CGFloat!
        if collectionOrigin.x > 0 {
            newX = collectionOrigin.x + collectionWidth / 2
            centerPoint = CGPoint(x: newX, y: collectionOrigin.y)
        } else {
            newX = collectionWidth / 2
            centerPoint = CGPoint(x: newX, y: collectionOrigin.y)
        }
        
        let index = self.collectionView.indexPathForItem(at: centerPoint)
        
        if(index != nil){
            
            self.selectedDate = self.datesArr[index!.item]
            self.collectionView.scrollToItem(at: IndexPath(row: index!.item, section: index!.section), at: .centeredHorizontally, animated: true)
            self.collectionView.reloadData()
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.nextBtn){
            checkData()
        }
    }
    
    func checkData(){
        if(self.selectedDate == nil || selectedTime == ""){
//            Utils.showSnakeBar(msg: , uv: self)
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: selectedDate == nil ? "Please select service booking date." : "Please select booking time.", key: selectedDate == nil ? "LBL_SELECT_SERVICE_BOOKING_DATE_MSG" : "LBL_SELECT_SERVICE_BOOKING_TIME"))
            return
        }
        
        let date = Utils.convertDateAppLocaleToGregorian(date: Utils.convertDateToFormate(date: self.selectedDate, formate: "yyyy-MM-dd"), dateFormate: "yyyy-MM-dd")
        let finalDate = "\(Configurations.convertNumToEnglish(numStr: Utils.convertDateToFormate(date: date, formate: "YYYY-MM-dd"))) \(Configurations.convertNumToEnglish(numStr:selectedTime.trimAll()))"
        self.finalDate = finalDate
        
        validateSelectedDate()
        
    }
    
    func validateSelectedDate(){
        
        let parameters = ["type":"CheckScheduleTimeAvailability","iUserId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "scheduleDate": self.finalDate]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    if(self.isFromMainScreen == false){
                        let mainScreenUv = GeneralFunctions.instantiateViewController(pageName: "MainScreenUV") as! MainScreenUV
                        mainScreenUv.ufxSelectedVehicleTypeId = self.ufxSelectedVehicleTypeId
                        mainScreenUv.ufxSelectedVehicleTypeName = self.ufxSelectedVehicleTypeName
                        mainScreenUv.ufxSelectedQty = self.ufxSelectedQty
                        mainScreenUv.ufxAddressId = self.ufxAddressId
                        mainScreenUv.ufxSelectedLatitude = self.ufxSelectedLatitude
                        mainScreenUv.ufxSelectedLongitude = self.ufxSelectedLongitude
                        mainScreenUv.selectedDate = self.finalDate
                        mainScreenUv.ufxServiceItemDict = self.ufxServiceItemDict
                        mainScreenUv.ufxCabBookingId = self.ufxCabBookingId
                        self.pushToNavController(uv: mainScreenUv)
                    }else{
                        self.performSegue(withIdentifier: "unwindToMainScreen", sender: self)
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
}

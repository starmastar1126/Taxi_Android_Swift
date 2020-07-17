//
//  SearchPlacesUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 25/09/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

protocol OnPlaceSelectDelegate {
    func onPlaceSelected(location:CLLocation, address:String, searchBar:UISearchBar, searchPlaceUv:SearchPlacesUV)
    func onPlaceSelectCancel(searchBar:UISearchBar, searchPlaceUv:SearchPlacesUV)
}

class SearchPlacesUV: UIViewController, UISearchBarDelegate, MyLabelClickDelegate, UITableViewDelegate, UITableViewDataSource, OnLocationUpdateDelegate, AddressFoundDelegate {
    
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var existingPlacesView: UIView!
    @IBOutlet weak var searchPlaceTableView: UITableView!
    
    @IBOutlet weak var placesHLbl: MyLabel!
    @IBOutlet weak var homeLocAreaView: UIView!
    @IBOutlet weak var homeLocHLbl: MyLabel!
    @IBOutlet weak var homeLocVLbl: MyLabel!
    @IBOutlet weak var homeLocEditImgView: UIImageView!
    @IBOutlet weak var homeLocImgView: UIImageView!
    @IBOutlet weak var workLocImgView: UIImageView!
    
    @IBOutlet weak var setLocMapAreaHeight: NSLayoutConstraint!
    @IBOutlet weak var setLocMapLbl: MyLabel!
    @IBOutlet weak var setLocRightArrowImgView: UIImageView!
    @IBOutlet weak var setLocOnMapAreaView: UIView!
    
    @IBOutlet weak var workLocAreaView: UIView!
    @IBOutlet weak var workLocHLbl: MyLabel!
    @IBOutlet weak var workLocVLbl: MyLabel!
    @IBOutlet weak var workLocEditImgView: UIImageView!
    
    @IBOutlet weak var recentLocationHLbl: MyLabel!
    @IBOutlet weak var recentLocTableView: UITableView!
    @IBOutlet weak var selectMyLocView: UIView!
    @IBOutlet weak var generalHAreaView: UIView!
    @IBOutlet weak var arrowImgView: UIImageView!
    @IBOutlet weak var generalAreaViewHeight: NSLayoutConstraint!
    @IBOutlet weak var myLocLbl: MyLabel!
    
    @IBOutlet weak var destinationLaterView: UIView!
    @IBOutlet weak var destinationLaterViewHeight: NSLayoutConstraint!
    @IBOutlet weak var destinationLaterLbl: MyLabel!
    @IBOutlet weak var destinationLaterArrowImgView: UIImageView!
    let generalFunc = GeneralFunctions()
    
    let searchBar = UISearchBar()
    
    var locationBias:CLLocation!
    
    var placeSelectDelegate:OnPlaceSelectDelegate?
    
    var isScreenLoaded = false
    
    var isScreenKilled = false
    
    var isFromMainScreen = false
    
    var isPickUpMode = true
    
    var isHomePlaceAdded = false
    var isWorkPlaceAdded = false
    
    var dataArrList = [RecentLocationItem]()
    var searchPlaceDataArr = [SearchLocationItem]()
    
    var cntView:UIView!
    
    var PAGE_HEIGHT:CGFloat = 310
    
    var keyboardHeightSet = false
    
    var cancelLbl:MyLabel!
    
    var loaderView:UIView!
    
    var placeSearchExeServerTask:ExeServerUrl!
    
    var fromAddAddress = false
    
    var isFromSelectLoc = false
    
    var isDriverAssigned = false
    
    var userProfileJson:NSDictionary!
    
    
    var getLocation:GetLocation!
    
    var currentLocation:CLLocation!
    
    var getAddressFrmLocation:GetAddressFromLocation!
    
    var SCREEN_TYPE = ""
    
    var currentSearchQuery = ""
    
    var defaultPageHeight:CGFloat = 0
    
    var errorLbl:MyLabel!
    
    var homeLoc:CLLocation!
    var workLoc:CLLocation!
    
    var currentCabType = ""
    
    var finalPageHeight:CGFloat = 0
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
        if(isScreenKilled){
            self.closeCurrentScreen()
        }
        
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        self.navigationController?.navigationBar.layer.zPosition = 1
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        //        searchBar.showsCancelButton = true
        searchBar.sizeToFit()
        
        searchBar.delegate = self
        //        searchBar.tintColor
        
        scrollView.keyboardDismissMode = .onDrag
        
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        currentCabType = currentCabType == "" ? userProfileJson.get("APP_TYPE") : currentCabType

        if(currentCabType.uppercased() == Utils.cabGeneralType_Ride.uppercased() && userProfileJson.get("APP_DESTINATION_MODE").uppercased() == "NONSTRICT" && isDriverAssigned == false){
           self.PAGE_HEIGHT = self.PAGE_HEIGHT + 50
        }
        
        //        navItem.titleView = searchBar
        let textWidth = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT").width(withConstrainedHeight: 29, font: UIFont(name: "Roboto-Light", size: 17)!)
        searchBar.frame.size = CGSize(width: Application.screenSize.width - 45 - textWidth, height: 40)
        self.navigationItem.leftBarButtonItem = UIBarButtonItem(customView:searchBar)
        
        cancelLbl = MyLabel()
        cancelLbl.font = UIFont(name: "Roboto-Light", size: 17)!
        cancelLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT")
        cancelLbl.setClickDelegate(clickDelegate: self)
        cancelLbl.fitText()
        cancelLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        self.navigationItem.titleView = UIView()
        self.navigationItem.rightBarButtonItem = UIBarButtonItem(customView:cancelLbl)
        
        
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)
    }
    
    override func viewDidAppear(_ animated: Bool) {
        
        self.navigationController?.navigationBar.layer.zPosition = -1
        
        if(isScreenLoaded == false){
            cntView = self.generalFunc.loadView(nibName: "SearchPlacesScreenDesign", uv: self, contentView: scrollView)
            
            cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT > scrollView.frame.height ? PAGE_HEIGHT : scrollView.frame.height)
            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT > scrollView.frame.height ? PAGE_HEIGHT : scrollView.frame.height)
            
            finalPageHeight = self.cntView.frame.size.height
            
            self.scrollView.addSubview(cntView)
            self.scrollView.bounces = false
            
            //            self.scrollView.addSubview(self.generalFunc.loadView(nibName: "SearchPlacesScreenDesign", uv: self, contentView: contentView))
            isScreenLoaded = true
            
            self.recentLocTableView.bounces = false
            
            setData()
            
            NotificationCenter.default.addObserver(self, selector: #selector(self.keyboardWillDisappear(sender:)), name: Notification.Name.UIKeyboardWillHide, object: nil)
            NotificationCenter.default.addObserver(self, selector: #selector(self.keyboardWillAppear(sender:)), name: Notification.Name.UIKeyboardWillShow, object: nil)
        }
        
        self.searchBar.becomeFirstResponder()
        
//        searchBar.becomeFirstResponder()
    }
    
    func releaseAllTask(){
        
        if(getAddressFrmLocation != nil){
            getAddressFrmLocation!.addressFoundDelegate = nil
            getAddressFrmLocation = nil
        }
        
        if(self.getLocation != nil){
            self.getLocation!.locationUpdateDelegate = nil
            self.getLocation!.releaseLocationTask()
            self.getLocation = nil
        }
        
        GeneralFunctions.removeObserver(obj: self)
    }
    
    func setData(){
        self.placesHLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
        self.placesHLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        
        self.existingPlacesView.isHidden = self.isFromSelectLoc == true ? true : false
        self.searchPlaceTableView.isHidden = true
        
        self.recentLocationHLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
        self.recentLocationHLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        self.recentLocationHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Recent Locations", key: "LBL_RECENT_LOCATIONS")
        self.placesHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Favorite Places", key: "LBL_FAV_LOCATIONS")
        
        self.setLocMapLbl.text = self.generalFunc.getLanguageLabel(origValue: "Set location on map", key: "LBL_SET_LOC_ON_MAP")
        self.destinationLaterLbl.text = self.generalFunc.getLanguageLabel(origValue: "Enter destination later", key: "LBL_DEST_ADD_LATER")
        
        self.recentLocTableView.dataSource = self
        self.recentLocTableView.delegate = self
        
        self.searchPlaceTableView.dataSource = self
        self.searchPlaceTableView.delegate = self
        
        checkPlaces()
        
        self.recentLocTableView.register(UINib(nibName: "RecentLocationTVCell", bundle: nil), forCellReuseIdentifier: "RecentLocationTVCell")
        self.searchPlaceTableView.register(UINib(nibName: "GPAutoCompleteListTVCell", bundle: nil), forCellReuseIdentifier: "GPAutoCompleteListTVCell")
        self.recentLocTableView.tableFooterView = UIView()
        self.searchPlaceTableView.tableFooterView = UIView()
        
        self.myLocLbl.text = self.generalFunc.getLanguageLabel(origValue: "I need service at my current location", key: "LBL_SERVICE_MY_LOCATION_HINT_INFO")
        if(fromAddAddress == true || self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            self.generalAreaViewHeight.constant = 50
            self.selectMyLocView.isHidden = false
            
            let selectMyLocTapGue = UITapGestureRecognizer()
            selectMyLocTapGue.addTarget(self, action: #selector(self.myLocImgTapped))
            self.selectMyLocView.isUserInteractionEnabled = true
            self.selectMyLocView.addGestureRecognizer(selectMyLocTapGue)
            
            if(self.locationBias == nil){
                getLocation = GetLocation(uv: self, isContinuous: false)
                getLocation.buildLocManager(locationUpdateDelegate: self)
            }else{
                self.currentLocation = self.locationBias
            }
            self.homeLocAreaView.isHidden = true
            self.workLocAreaView.isHidden = true
        }else{
            //            if(self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            //                self.generalAreaViewHeight.constant = 0
            //
            //            }
            //            self.selectMyLocView.isHidden = true
        }
        
        if(self.isFromSelectLoc == true){
            self.setLocMapAreaHeight.constant = 0
            self.setLocOnMapAreaView.isHidden = true
            
            self.existingPlacesView.isHidden = true
        }
        
        GeneralFunctions.setImgTintColor(imgView: self.setLocRightArrowImgView, color: UIColor(hex: 0x1c1c1c))
        GeneralFunctions.setImgTintColor(imgView: self.destinationLaterArrowImgView, color: UIColor(hex: 0x1c1c1c))
        
        if(Configurations.isRTLMode()){
            self.setLocRightArrowImgView.transform = CGAffineTransform(scaleX: -1, y: 1)
        }
        
        if(currentCabType.uppercased() == Utils.cabGeneralType_Ride.uppercased() && userProfileJson.get("APP_DESTINATION_MODE").uppercased() == "NONSTRICT" && isDriverAssigned == false && isPickUpMode == false){
            self.destinationLaterView.isHidden = false
            self.destinationLaterViewHeight.constant = 50
            
            let destLaterTapGue = UITapGestureRecognizer()
            destLaterTapGue.addTarget(self, action: #selector(self.addDestLaterTapped))
            self.destinationLaterView.isUserInteractionEnabled = true
            self.destinationLaterView.addGestureRecognizer(destLaterTapGue)
        }
        
        let locOnMapTapGue = UITapGestureRecognizer()
        locOnMapTapGue.addTarget(self, action: #selector(self.findLocOnMap))
        
        self.setLocOnMapAreaView.isUserInteractionEnabled = true
        self.setLocOnMapAreaView.addGestureRecognizer(locOnMapTapGue)
    }
    
    func myLocImgTapped(){
        if(GeneralFunctions.hasLocationEnabled() == false){
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_GPSENABLE_TXT"))
        }else{
            findUserCurrentLocationDetails()
        }
    }
    
    func findUserCurrentLocationDetails(){
        if(self.currentLocation == nil){
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_GPSENABLE_TXT"))
            return
        }
        
        getAddressFrmLocation = GetAddressFromLocation(uv: self)
        getAddressFrmLocation.addressFoundDelegate = self
            
        getAddressFrmLocation.setLocation(latitude: currentLocation.coordinate.latitude, longitude: currentLocation.coordinate.longitude)
        getAddressFrmLocation.executeProcess(isOpenLoader: true, isAlertShow:true)
        
    }
    
    func onAddressFound(address: String, location: CLLocation, isPickUpMode: Bool, dataResult: String) {
//        self.userLocationAddress = address
        if(self.placeSelectDelegate != nil){
            self.placeSelectDelegate?.onPlaceSelected(location: location, address: address, searchBar: self.searchBar, searchPlaceUv: self)
        }
    }
    
    func onLocationUpdate(location: CLLocation) {
        self.currentLocation = location
        self.locationBias = CLLocation(latitude: location.coordinate.latitude, longitude: location.coordinate.longitude)
        self.getLocation.locationUpdateDelegate = nil
        self.getLocation.releaseLocationTask()
        self.getLocation = nil
    }
    

    func keyboardWillDisappear(sender: NSNotification){
        let info = sender.userInfo!
        let keyboardSize = (info[UIKeyboardFrameEndUserInfoKey] as! NSValue).cgRectValue.height
        
//        if(keyboardHeightSet){
        changeContentSize(PAGE_HEIGHT: finalPageHeight)
//        changeContentSize(PAGE_HEIGHT: (self.PAGE_HEIGHT - keyboardSize))
            keyboardHeightSet = false
//        }
    }
    func keyboardWillAppear(sender: NSNotification){
        let info = sender.userInfo!
        let keyboardSize = (info[UIKeyboardFrameEndUserInfoKey] as! NSValue).cgRectValue.height
        
        finalPageHeight = self.PAGE_HEIGHT
//        if(Application.screenSize.height < (keyboardSize + self.PAGE_HEIGHT)){
            changeContentSize(PAGE_HEIGHT: (keyboardSize + self.PAGE_HEIGHT))
            keyboardHeightSet = true
//        }
    }
    
    func changeContentSize(PAGE_HEIGHT:CGFloat){
        self.PAGE_HEIGHT = PAGE_HEIGHT
        
        cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
//        cntView.backgroundColor = UIColor.blue
//        recentLocTableView.backgroundColor = UIColor.clear
//        existingPlacesView.backgroundColor = UIColor.clear
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
        
    }
    
    func closeKeyboard(){
        self.view.endEditing(true)
    }
    
    func searchBarTextDidEndEditing(_ searchBar: UISearchBar) {
        //        for(id subview in [yourSearchBar subviews])
        //        {
        //            if ([subview isKindOfClass:[UIButton class]]) {
        //                [subview setEnabled:YES];
        //            }
        //        }
        
        Utils.printLog(msgData: "EndEditing")
        
    }
    
    func searchBarTextDidBeginEditing(_ searchBar: UISearchBar) {
//        Utils.printLog(msgData: "Begin Editing")
        
    }
    
    func searchBar(_ searchBar: UISearchBar, textDidChange searchText: String) {
        self.currentSearchQuery = searchText.trim()
        fetchAutoCompletePlaces(searchText: searchText.trim())
    }
    
    func searchBarCancelButtonClicked(_ searchBar: UISearchBar) {
        //        self.closeCurrentScreen()
        if(self.placeSelectDelegate != nil){
            self.placeSelectDelegate?.onPlaceSelectCancel(searchBar: self.searchBar, searchPlaceUv: self)
        }
    }
    
    
    func fetchAutoCompletePlaces(searchText:String){
        
        if(searchText.characters.count < 2){
            self.existingPlacesView.isHidden = self.isFromSelectLoc == true ? true : false
            self.searchPlaceTableView.isHidden = true
            if(self.loaderView != nil){
                self.loaderView.isHidden = true
            }
            
            if(self.errorLbl != nil){
                self.errorLbl.isHidden = true
            }
            
//            changeContentSize(PAGE_HEIGHT: defaultPageHeight)
            
            return
        }else{
//            defaultPageHeight = self.PAGE_HEIGHT

            self.existingPlacesView.isHidden = true
            self.searchPlaceTableView.isHidden = false
            
//            changeContentSize(PAGE_HEIGHT: Application.screenSize.height - 100)
        }
        
        self.searchPlaceDataArr.removeAll()
        self.searchPlaceTableView.reloadData()
        
        if(self.errorLbl != nil){
            self.errorLbl.isHidden = true
        }
        
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
            loaderView.backgroundColor = UIColor.clear
        }else{
            loaderView.isHidden = false
        }
        
        var autoCompleteUrl = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=\(searchText)&key=\(Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY"))&language=\(Configurations.getGoogleMapLngCode())&sensor=true"
        
        if(locationBias != nil){
            autoCompleteUrl = autoCompleteUrl + "&location=\(locationBias.coordinate.latitude),\(locationBias.coordinate.longitude)&radius=20000"
        }
        Utils.printLog(msgData: "autoCompleteUrl::\(autoCompleteUrl)")
        if(placeSearchExeServerTask != nil){
            placeSearchExeServerTask.cancel()
            placeSearchExeServerTask = nil
        }
        
        
        let exeWebServerUrl = ExeServerUrl(dict_data: [String:String](), currentView: self.view, isOpenLoader: false)
        self.placeSearchExeServerTask = exeWebServerUrl
        exeWebServerUrl.executeGetProcess(completionHandler: { (response) -> Void in
            
            if(self.currentSearchQuery != searchText){
                return
            }
            
            if(response != ""){
                
                
                if(self.errorLbl != nil){
                    self.errorLbl.isHidden = true
                }
                
                if(self.searchPlaceTableView.isHidden == true){
                    self.loaderView.isHidden = true
                    return
                }
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("status").uppercased() == "OK"){
                    
                    let predictionsArr = dataDict.getArrObj("predictions")
                    
                    for i in 0..<predictionsArr.count{
                        let item = predictionsArr[i] as! NSDictionary
                        
                        if(item.get("place_id") != ""){
                            let structured_formatting = item.getObj("structured_formatting")
                            let searchLocItem = SearchLocationItem(placeId: item.get("place_id"), mainAddress: structured_formatting.get("main_text"), subAddress: structured_formatting.get("secondary_text"), description: item.get("description"))
                            
                            self.searchPlaceDataArr += [searchLocItem]
                        }
                        
                    }
                    
                    
                    self.searchPlaceTableView.reloadData()
                    
                }else if(dataDict.get("status") == "ZERO_RESULTS"){
                    if(self.errorLbl != nil){
                        self.errorLbl.isHidden = false
                        self.errorLbl.text = self.generalFunc.getLanguageLabel(origValue: InternetConnection.isConnectedToNetwork() ? "We didn't find any places matched to your entered place. Please try again with another text." : "No Internet Connection", key: InternetConnection.isConnectedToNetwork() ? "LBL_NO_PLACES_FOUND" : "LBL_NO_INTERNET_TXT")
                    }else{
                        self.errorLbl = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: InternetConnection.isConnectedToNetwork() ? "We didn't find any places matched to your entered place. Please try again with another text." : "No Internet Connection", key: InternetConnection.isConnectedToNetwork() ? "LBL_NO_PLACES_FOUND" : "LBL_NO_INTERNET_TXT"))
                        
                        self.errorLbl.isHidden = false
                    }
                
                }else{
                    if(self.errorLbl != nil){
                        self.errorLbl.isHidden = false
                        self.errorLbl.text = self.generalFunc.getLanguageLabel(origValue: InternetConnection.isConnectedToNetwork() ? "Error occurred while searching nearest places. Please try again later." : "No Internet Connection", key: InternetConnection.isConnectedToNetwork() ? "LBL_PLACE_SEARCH_ERROR" : "LBL_NO_INTERNET_TXT")
                    }else{
                        self.errorLbl = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: InternetConnection.isConnectedToNetwork() ? "Error occurred while searching nearest places. Please try again later." : "No Internet Connection", key: InternetConnection.isConnectedToNetwork() ? "LBL_PLACE_SEARCH_ERROR" : "LBL_NO_INTERNET_TXT"))
                        
                        self.errorLbl.isHidden = false
                    }
                }
                
                
            }else{
                //                self.generalFunc.setError(uv: self)
                if(self.errorLbl != nil){
                    self.errorLbl.isHidden = false
                    self.errorLbl.text = self.generalFunc.getLanguageLabel(origValue: InternetConnection.isConnectedToNetwork() ? "Error occurred while searching nearest places. Please try again later." : "No Internet Connection", key: InternetConnection.isConnectedToNetwork() ? "LBL_PLACE_SEARCH_ERROR" : "LBL_NO_INTERNET_TXT")
                }else{
                    self.errorLbl = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: InternetConnection.isConnectedToNetwork() ? "Error occurred while searching nearest places. Please try again later." : "No Internet Connection", key: InternetConnection.isConnectedToNetwork() ? "LBL_PLACE_SEARCH_ERROR" : "LBL_NO_INTERNET_TXT"))
                    self.errorLbl.isHidden = false
                }
            }
            
            self.loaderView.isHidden = true
        }, url: autoCompleteUrl)
    }
    
    
    
    func addDestLaterTapped(){
        if(self.placeSelectDelegate != nil){
            self.placeSelectDelegate?.onPlaceSelected(location: CLLocation(latitude: 0.0, longitude: 0.0), address: "DEST_SKIPPED", searchBar: self.searchBar, searchPlaceUv: self)
        }
    }
    
    func findLocOnMap(){
        let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
        addDestinationUv.SCREEN_TYPE = self.SCREEN_TYPE
        if(isFromMainScreen == true){
            addDestinationUv.isFromMainScreen = self.isFromMainScreen
        }
        addDestinationUv.isFromSearchPlaces = true
        addDestinationUv.isFromSelectLoc = true
        addDestinationUv.centerLocation = self.locationBias
        
        self.pushToNavController(uv: addDestinationUv)
    }
    
    func homePlaceTapped(){
        //        self.closeCurrentScreen()
        //        self.mainScreenUV.continueLocationSelected(selectedLocation: , selectedAddress: (GeneralFunctions.getValue(key: "userHomeLocationAddress") as! String))
        
        if(self.placeSelectDelegate != nil){
            self.placeSelectDelegate?.onPlaceSelected(location: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userHomeLocationLatitude") as! String), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userHomeLocationLongitude") as! String)), address: (GeneralFunctions.getValue(key: "userHomeLocationAddress") as! String), searchBar: self.searchBar, searchPlaceUv: self)
        }
    }
    
    func workPlaceTapped(){
        //        self.closeCurrentScreen()
        //        self.mainScreenUV.continueLocationSelected(selectedLocation: , selectedAddress: )
        
        if(self.placeSelectDelegate != nil){
            self.placeSelectDelegate?.onPlaceSelected(location: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userWorkLocationLatitude") as! String), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userWorkLocationLongitude") as! String)), address: (GeneralFunctions.getValue(key: "userWorkLocationAddress") as! String), searchBar: self.searchBar, searchPlaceUv: self)
        }
    }
    
    func myLableTapped(sender: MyLabel) {
        if(sender == self.placesHLbl){
            
        }else if(sender == self.homeLocHLbl || sender == self.homeLocVLbl){
            if(isHomePlaceAdded){
                homePlaceTapped()
            }else{
                homePlaceEditTapped()
            }
        }else if(sender == self.workLocHLbl || sender == self.workLocVLbl){
            if(isWorkPlaceAdded){
                workPlaceTapped()
            }else{
                workPlaceEditTapped()
            }
        }else if(self.cancelLbl != nil && sender == cancelLbl){
            searchBarCancelButtonClicked(self.searchBar)
        }
    }
    
    
    func getHomePlaceTapGue() -> UITapGestureRecognizer{
        let homePlaceTapGue = UITapGestureRecognizer()
        homePlaceTapGue.addTarget(self, action: #selector(self.homePlaceTapped))
        
        return homePlaceTapGue
    }
    
    func getWorkPlaceTapGue() -> UITapGestureRecognizer{
        let workPlaceTapGue = UITapGestureRecognizer()
        workPlaceTapGue.addTarget(self, action: #selector(self.workPlaceTapped))
        
        return workPlaceTapGue
    }
    
    func checkRecentPlaces(){
        
        
        self.dataArrList.removeAll()
        
        
        let sourceLocations = userProfileJson.getArrObj("SourceLocations")
        let destLocations = userProfileJson.getArrObj("DestinationLocations")
        
        if(self.isPickUpMode == true){
            for i in 0..<sourceLocations.count{
                let currentItem = sourceLocations[i] as! NSDictionary
                
                let recentLocItem = RecentLocationItem(location: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: currentItem.get("tStartLat")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: currentItem.get("tStartLong"))), address: currentItem.get("tSaddress"))
                
                self.dataArrList += [recentLocItem]
            }
        }else{
            for i in 0..<destLocations.count{
                let currentItem = destLocations[i] as! NSDictionary
                
                let recentLocItem = RecentLocationItem(location: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: currentItem.get("tEndLat")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: currentItem.get("tEndLong"))), address: currentItem.get("tDaddress"))
                
                self.dataArrList += [recentLocItem]
            }
        }
        
        if(self.dataArrList.count < 1){
            self.recentLocTableView.isHidden = true
            self.recentLocationHLbl.isHidden = true
        }else{
            self.recentLocTableView.isHidden = false
            self.recentLocationHLbl.isHidden = false
        }
        
        PAGE_HEIGHT = PAGE_HEIGHT + CGFloat(60 * self.dataArrList.count)
        self.finalPageHeight = PAGE_HEIGHT
        
        cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
        self.recentLocTableView.isScrollEnabled = false
        self.recentLocTableView.reloadData()
        
    }
    
    func checkPlaces(){
        
        checkRecentPlaces()
        
        let userHomeLocationAddress = GeneralFunctions.getValue(key: "userHomeLocationAddress") != nil ? (GeneralFunctions.getValue(key: "userHomeLocationAddress") as! String) : ""
        let userWorkLocationAddress = GeneralFunctions.getValue(key: "userWorkLocationAddress") != nil ? (GeneralFunctions.getValue(key: "userWorkLocationAddress") as! String) : ""
        
        if(userHomeLocationAddress != ""){
            isHomePlaceAdded = true
            
            self.homeLocEditImgView.image = UIImage(named: "ic_edit")
            self.homeLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HOME_PLACE")
            self.homeLocVLbl.text = GeneralFunctions.getValue(key: "userHomeLocationAddress") as? String
            
            let homeLatitude = GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userHomeLocationLatitude") as! String)
            let homeLongitude = GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userHomeLocationLongitude") as! String)
            
            self.homeLoc = CLLocation(latitude: homeLatitude, longitude: homeLongitude)
            
        }else{
            self.homeLocEditImgView.image = UIImage(named: "ic_add_plus")
            self.homeLocVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_HOME_PLACE_TXT")
            self.homeLocHLbl.text = "----"
            
            isHomePlaceAdded = false
        }
        
        self.homeLocHLbl.setClickDelegate(clickDelegate: self)
        self.homeLocVLbl.setClickDelegate(clickDelegate: self)
        
        self.homeLocImgView.isUserInteractionEnabled = true
        self.homeLocImgView.addGestureRecognizer(self.getHomePlaceTapGue())
        
        if(userWorkLocationAddress != ""){
            isWorkPlaceAdded = true
            
            self.workLocEditImgView.image = UIImage(named: "ic_edit")
            self.workLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_WORK_PLACE")
            self.workLocVLbl.text = GeneralFunctions.getValue(key: "userWorkLocationAddress") as? String
            
            let workLatitude = GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userWorkLocationLatitude") as! String)
            let workLongitude = GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userWorkLocationLongitude") as! String)
            
            self.workLoc = CLLocation(latitude: workLatitude, longitude: workLongitude)
            
        }else{
            self.workLocEditImgView.image = UIImage(named: "ic_add_plus")
            self.workLocVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_WORK_PLACE_TXT")
            self.workLocHLbl.text = "----"
            
            isWorkPlaceAdded = false
        }
        
        self.workLocHLbl.setClickDelegate(clickDelegate: self)
        self.workLocVLbl.setClickDelegate(clickDelegate: self)
        
        self.workLocImgView.isUserInteractionEnabled = true
        self.workLocImgView.addGestureRecognizer(self.getWorkPlaceTapGue())
        
        GeneralFunctions.setImgTintColor(imgView: self.homeLocImgView, color: UIColor(hex: 0x353e45))
        GeneralFunctions.setImgTintColor(imgView: self.workLocImgView, color: UIColor(hex: 0x353e45))
        GeneralFunctions.setImgTintColor(imgView: self.homeLocEditImgView, color: UIColor(hex: 0x858f93))
        GeneralFunctions.setImgTintColor(imgView: self.workLocEditImgView, color: UIColor(hex: 0x858f93))
        
        let homePlaceTapGue = UITapGestureRecognizer()
        let workPlaceTapGue = UITapGestureRecognizer()
        
        homePlaceTapGue.addTarget(self, action: #selector(self.homePlaceEditTapped))
        workPlaceTapGue.addTarget(self, action: #selector(self.workPlaceEditTapped))
        
        self.homeLocEditImgView.isUserInteractionEnabled = true
        self.homeLocEditImgView.addGestureRecognizer(homePlaceTapGue)
        
        self.workLocEditImgView.isUserInteractionEnabled = true
        self.workLocEditImgView.addGestureRecognizer(workPlaceTapGue)
    }
    
    func homePlaceEditTapped(){
        let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
        addDestinationUv.SCREEN_TYPE = "HOME"
        addDestinationUv.centerLocation = self.homeLoc
        addDestinationUv.isFromSearchPlaces = true
        if(isFromMainScreen == true){
            addDestinationUv.isFromMainScreen = self.isFromMainScreen
        }
        self.pushToNavController(uv: addDestinationUv)
    }
    
    func workPlaceEditTapped(){
        let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
        addDestinationUv.SCREEN_TYPE = "WORK"
        addDestinationUv.centerLocation = self.workLoc
        addDestinationUv.isFromSearchPlaces = true
        if(isFromMainScreen == true){
            addDestinationUv.isFromMainScreen = self.isFromMainScreen
        }
        self.pushToNavController(uv: addDestinationUv)
    }
    
    
    func numberOfSections(in tableView: UITableView) -> Int {
        // #warning Incomplete implementation, return the number of sections
        return 1
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        
        if(tableView == self.searchPlaceTableView){
            return self.searchPlaceDataArr.count
        }
        return self.dataArrList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        
        if(tableView == self.searchPlaceTableView){
            let cell = tableView.dequeueReusableCell(withIdentifier: "GPAutoCompleteListTVCell", for: indexPath) as! GPAutoCompleteListTVCell
            
            let item = self.searchPlaceDataArr[indexPath.item]
            
            cell.mainTxtLbl.text = item.mainAddress
            cell.secondaryTxtLbl.text = item.subAddress
            
            cell.selectionStyle = .none
            cell.backgroundColor = UIColor.clear
            cell.contentView.layoutIfNeeded()
            return cell
        }
        let cell = tableView.dequeueReusableCell(withIdentifier: "RecentLocationTVCell", for: indexPath) as! RecentLocationTVCell
        
        let item = self.dataArrList[indexPath.item]
        
        cell.recentAddressLbl.text = item.address
        
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        return cell
    }
    
   
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        tableView.estimatedRowHeight = 1500
        tableView.rowHeight = UITableViewAutomaticDimension
        return UITableViewAutomaticDimension
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        if(tableView == self.searchPlaceTableView){
            let item = self.searchPlaceDataArr[indexPath.item]
            findPlaceDetail(placeId: item.placeId, description: item.description)
            return
        }
        
        let item = self.dataArrList[indexPath.item]
        
        //        self.closeCurrentScreen()
        //        self.mainScreenUV.continueLocationSelected(selectedLocation: item.location, selectedAddress: item.address)
        
        if(self.placeSelectDelegate != nil){
            self.placeSelectDelegate?.onPlaceSelected(location: item.location, address: item.address, searchBar: self.searchBar, searchPlaceUv: self)
        }
    }
    
    func findPlaceDetail(placeId:String, description:String){
        
        let placeDetailUrl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=\(placeId)&key=\(Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY"))&language=\(Configurations.getGoogleMapLngCode())&sensor=true"
            
        let exeWebServerUrl = ExeServerUrl(dict_data: [String:String](), currentView: self.view, isOpenLoader: true)
        self.placeSearchExeServerTask = exeWebServerUrl
        exeWebServerUrl.executeGetProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("status").uppercased() == "OK"){
                    
                    let resultObj = dataDict.getObj("result")
                    let geometryObj = resultObj.getObj("geometry")
                    let locationObj = geometryObj.getObj("location")
                    let latitude = locationObj.get("lat")
                    let longitude = locationObj.get("lng")
                    
                    let location = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: latitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: longitude))
                    
                    if(self.placeSelectDelegate != nil){
                        self.placeSelectDelegate?.onPlaceSelected(location: location, address: description, searchBar: self.searchBar, searchPlaceUv: self)
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self)
                }
                
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
        }, url: placeDetailUrl)
        
    }
    
    @IBAction func unwindToSearchPlaceScreen(_ segue:UIStoryboardSegue) {
        //        unwindToSignUp
        
        if(segue.source.isKind(of: AddDestinationUV.self))
        {
            let addDestinationUv = segue.source as! AddDestinationUV
            let selectedLocation = addDestinationUv.selectedLocation
            let selectedAddress = addDestinationUv.selectedAddress
            
            GeneralFunctions.setSelectedLocations(latitude: selectedLocation!.coordinate.latitude, longitude: selectedLocation!.coordinate.longitude, address: selectedAddress, type: addDestinationUv.SCREEN_TYPE)
            
            //            self.mainScreenUV.continueLocationSelected(selectedLocation: selectedLocation, selectedAddress: selectedAddress)
            if(self.placeSelectDelegate != nil){
                self.placeSelectDelegate?.onPlaceSelected(location: selectedLocation!, address: selectedAddress, searchBar: self.searchBar, searchPlaceUv: self)
            }
            
        }
    }
}

class SearchLocationItem {
    
    var placeId:String!
    var mainAddress:String!
    var subAddress:String!
    var description:String!
    
    // MARK: Initialization
    
    init(placeId: String, mainAddress:String, subAddress:String, description:String) {
        // Initialize stored properties.
        self.placeId = placeId
        self.mainAddress = mainAddress
        self.subAddress = subAddress
        self.description = description
        
    }
}

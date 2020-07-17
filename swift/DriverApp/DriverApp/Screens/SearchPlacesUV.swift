//
//  SearchPlacesUV.swift
//  DriverApp
//
//  Created by NEW MAC on 20/11/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit


protocol OnPlaceSelectDelegate {
    func onPlaceSelected(location:CLLocation, address:String, searchBar:UISearchBar, searchPlaceUv:SearchPlacesUV)
    func onPlaceSelectCancel(searchBar:UISearchBar, searchPlaceUv:SearchPlacesUV)
}

class SearchPlacesUV: UIViewController, UISearchBarDelegate, UITableViewDelegate, UITableViewDataSource, MyLabelClickDelegate {

    @IBOutlet weak var contentView: UIView!
    
    @IBOutlet weak var searchPlaceTableView: UITableView!
    
    let generalFunc = GeneralFunctions()
    
    let searchBar = UISearchBar()
    
    var locationBias:CLLocation!
    
    var placeSelectDelegate:OnPlaceSelectDelegate?
    
    var isScreenLoaded = false
    
    
    var searchPlaceDataArr = [SearchLocationItem]()
    
    var cntView:UIView!
    
    var cancelLbl:MyLabel!
    
    var loaderView:UIView!
    
    var placeSearchExeServerTask:ExeServerUrl!
    
    var currentLocation:CLLocation!
    
    var currentSearchQuery = ""
    
    var errorLbl:MyLabel!
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        searchBar.sizeToFit()
        
        searchBar.delegate = self
        
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
        
        
        if(isScreenLoaded == false){
            cntView = self.generalFunc.loadView(nibName: "SearchPlacesScreenDesign", uv: self, contentView: contentView)
            
            
            cntView.frame.size = CGSize(width: cntView.frame.width, height: contentView.frame.height)
            
            self.contentView.addSubview(cntView)
            
            isScreenLoaded = true
            self.searchPlaceTableView.bounces = false
            setData()
        }
        
        searchBar.becomeFirstResponder()
    }
    
    func setData(){
        
        self.searchPlaceTableView.isHidden = false
        
        self.searchPlaceTableView.dataSource = self
        self.searchPlaceTableView.delegate = self
        
        
        self.searchPlaceTableView.register(UINib(nibName: "GPAutoCompleteListTVCell", bundle: nil), forCellReuseIdentifier: "GPAutoCompleteListTVCell")
        self.searchPlaceTableView.tableFooterView = UIView()
        
    }
    
    
    
    func myLableTapped(sender: MyLabel) {
        if(self.cancelLbl != nil && sender == cancelLbl){
            searchBarCancelButtonClicked(self.searchBar)
        }
    }

    func releaseAllTask(){
        
        
        GeneralFunctions.removeObserver(obj: self)
    }
    
    func searchBarTextDidEndEditing(_ searchBar: UISearchBar) {
        Utils.printLog(msgData: "EndEditing")
        
    }
    
    func searchBarTextDidBeginEditing(_ searchBar: UISearchBar) {
        Utils.printLog(msgData: "Begin Editing")
        
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
            self.searchPlaceTableView.isHidden = true
            if(self.loaderView != nil){
                self.loaderView.isHidden = true
            }
            
            if(self.errorLbl != nil){
                self.errorLbl.isHidden = true
            }
            
            return
        }else{
            self.searchPlaceTableView.isHidden = false
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

    func numberOfSections(in tableView: UITableView) -> Int {
        // #warning Incomplete implementation, return the number of sections
        return 1
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        
            return self.searchPlaceDataArr.count
        
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        
            let cell = tableView.dequeueReusableCell(withIdentifier: "GPAutoCompleteListTVCell", for: indexPath) as! GPAutoCompleteListTVCell
            
            let item = self.searchPlaceDataArr[indexPath.item]
            
            cell.mainTxtLbl.text = item.mainAddress
            cell.secondaryTxtLbl.text = item.subAddress
            
            cell.selectionStyle = .none
            cell.backgroundColor = UIColor.clear
            
            return cell
        
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
            let item = self.searchPlaceDataArr[indexPath.item]
            findPlaceDetail(placeId: item.placeId, description: item.description)
        
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        tableView.estimatedRowHeight = 1500
        tableView.rowHeight = UITableViewAutomaticDimension
        return UITableViewAutomaticDimension
    }
    
    func findPlaceDetail(placeId:String, description:String){
        
        let placeDetailUrl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=\(placeId)&key=\(Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY"))&language=\(Configurations.getGoogleMapLngCode())&sensor=true"
        
        Utils.printLog(msgData: "PlaceDetailURL:\(placeDetailUrl)")
        
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

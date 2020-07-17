//
//  MyOnGoingTripsUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 18/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class MyOnGoingTripsUV: UIViewController, UITableViewDelegate, UITableViewDataSource, MyBtnClickDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    
    let generalFunc = GeneralFunctions()
    
    var dataArrList = [NSDictionary]()
    var extraHeightContainer = [CGFloat]()
    
    var loaderView:UIView!
    
    var cntView:UIView!
    
    var isDataSet = false
    var isSafeAreaSet = false
    var currentWebTask:ExeServerUrl!
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        GeneralFunctions.postNotificationSignal(key: ConfigPubNub.resumeInst_key, obj: self)
        
        if(dataArrList.count > 0){
            getData()
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.addBackBarBtn()
        
        cntView = self.generalFunc.loadView(nibName: "MyOnGoingTripsScreenDesign", uv: self, contentView: contentView)
        cntView.isHidden = true
        self.contentView.addSubview(cntView)
        
        setData()
        
        self.tableView.delegate = self
        
        self.tableView.dataSource = self
        self.tableView.tableFooterView = UIView()
        self.tableView.register(UINib(nibName: "MyOnGoingTripsListTVCell", bundle: nil), forCellReuseIdentifier: "MyOnGoingTripsListTVCell")
        self.tableView.contentInset = UIEdgeInsets(top: 8, left: 0, bottom: GeneralFunctions.getSafeAreaInsets().bottom + 8, right: 0)
        
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
    
    override func viewDidAppear(_ animated: Bool) {
        if(isDataSet == false){
//            cntView.frame = self.contentView.frame
            
            cntView.isHidden = false
            getData()
            
            isDataSet = true
        }
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "My On Going Trips", key: "LBL_MY_ONGOING_TRIPS_HEADER_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "My On Going Trips", key: "LBL_MY_ONGOING_TRIPS_HEADER_TXT")
        
    }
    
    
    
    func getData(){
        
        self.dataArrList.removeAll()
        self.extraHeightContainer.removeAll()
        self.tableView.reloadData()
        
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
            loaderView.backgroundColor = UIColor.clear
        }else{
            loaderView.isHidden = false
        }
        
        if(currentWebTask != nil){
            currentWebTask.cancel()
            currentWebTask = nil
        }
        
        let parameters = ["type":"getOngoingUserTrips","iUserId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        currentWebTask = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let dataArr = dataDict.getArrObj(Utils.message_str)
                    
                    
                    self.dataArrList.removeAll()
                    self.extraHeightContainer.removeAll()
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        let sourceAddHeight = dataTemp.get("tSaddress").height(withConstrainedWidth: Application.screenSize.width - 130, font: UIFont(name: "Roboto-Light", size: 14)!) - 20
                        
                        var vTypeNameHeight = dataTemp.get("SelectedTypeName").height(withConstrainedWidth: Application.screenSize.width - 46, font: UIFont(name: "Roboto-Light", size: 16)!) - 20

                        if(vTypeNameHeight < 0){
                            vTypeNameHeight = 0
                        }
                        
                        self.dataArrList += [dataTemp]
                        self.extraHeightContainer += [sourceAddHeight + vTypeNameHeight]
                        
                    }
                    
                    self.tableView.reloadData()
                    
                }else{
                    _ = GeneralFunctions.addMsgLbl(contentView: self.cntView, msg: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
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
        let cell = tableView.dequeueReusableCell(withIdentifier: "MyOnGoingTripsListTVCell", for: indexPath) as! MyOnGoingTripsListTVCell
        
        let item = self.dataArrList[indexPath.item]
        cell.bookingNoLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BOOKING") + "# " + Configurations.convertNumToAppLocal(numStr: item.get("vRideNo"))
        cell.providerNameLbl.text = item.get("driverName")
//        cell.providerNameLbl.textColor = UIColor.UCAColor.AppThemeColor
        cell.dateLbl.text = Utils.convertDateFormateInAppLocal(date: Utils.convertDateGregorianToAppLocale(date: item.get("dDateOrig"), dateFormate: "yyyy-MM-dd HH:mm:ss"), toDateFormate: Utils.dateFormateWithTime)
        
        cell.providerImgView.sd_setImage(with: URL(string: CommonUtils.driver_image_url + "\(item.get("iDriverId"))/\(item.get("driverImage"))"), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
            
        })
        
        Utils.createRoundedView(view: cell.providerImgView, borderColor: UIColor.UCAColor.AppThemeColor, borderWidth: 1)
        
        cell.sourceAddressLbl.text = item.get("tSaddress")
        cell.sourceAddressLbl.fitText()
        cell.ratingView.rating = GeneralFunctions.parseFloat(origValue: 0, data: item.get("driverRating"))
        cell.serviceTypeLbl.text = item.get("SelectedTypeName")
        
//        cell.serviceTypeLbl.fitText()
        
        cell.viewDetailBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "View Details", key: "LBL_VIEW_DETAILS"))
        
        cell.mainView.layer.shadowOpacity = 0.5
        cell.mainView.layer.shadowOffset = CGSize(width: 0, height: 3)
        cell.mainView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        Utils.createRoundedView(view: cell.mainView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        cell.viewDetailBtn.tag = indexPath.item
        cell.viewDetailBtn.btnType = "VIEW_DETAIL"
        cell.viewDetailBtn.clickDelegate = self
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        return cell
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat{
        
        if(indexPath.item < self.extraHeightContainer.count){
            return self.extraHeightContainer[indexPath.item] + 260
        }
        
        return 260
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        
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
        if(sender.btnType == "VIEW_DETAIL"){
            let myOnGoingTripDetailsUV = GeneralFunctions.instantiateViewController(pageName: "MyOnGoingTripDetailsUV") as! MyOnGoingTripDetailsUV
            myOnGoingTripDetailsUV.dataDict = self.dataArrList[sender.tag]
            
            self.pushToNavController(uv: myOnGoingTripDetailsUV)
        }
    }
    
    @IBAction func unwindToMyOnGoingTripsScreen(_ segue:UIStoryboardSegue) {
        //        unwindToSignUp
        
        if(segue.source.isKind(of: MyOnGoingTripDetailsUV.self)){
            // Called when booking is successfully finished
            self.dataArrList.removeAll()
            self.tableView.reloadData()
            
            self.getData()
        }
    }
}

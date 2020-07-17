//
//  UpdateServicesUV.swift
//  DriverApp
//
//  Created by NEW MAC on 03/10/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class UpdateServicesUV: UIViewController, UITableViewDelegate, UITableViewDataSource, BEMCheckBoxDelegate, MyBtnClickDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    @IBOutlet weak var updateServicesBtn: MyButton!
    
    var cntView:UIView!
    
    var generalFunc = GeneralFunctions()
    
    var dataArrList = [NSDictionary]()
    var chkStatusArr = [Bool]()
    
    var loaderView:UIView!
    
    var iVehicleCategoryId = ""
    var vTitle = ""
    
    var isDataSet = false
    
    override func viewDidLoad() {
        super.viewDidLoad()

        cntView = self.generalFunc.loadView(nibName: "UpdateServicesScreenDesign", uv: self, contentView: contentView)
        
        cntView.isHidden = true
        
        self.contentView.addSubview(cntView)
        
        self.addBackBarBtn()
        self.setData()
        
        self.tableView.delegate = self
        self.tableView.dataSource = self
        self.tableView.bounces = false
        self.tableView.contentInset = UIEdgeInsets(top: 5, left: 0, bottom: 5, right: 0)
        self.tableView.tableFooterView = UIView()
        self.tableView.register(UINib(nibName: "UpdateServicesTVCell", bundle: nil), forCellReuseIdentifier: "UpdateServicesTVCell")
        
        self.updateServicesBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Update Services", key: "LBL_UPDATE_SERVICES"))
        self.updateServicesBtn.clickDelegate = self
        
        self.dataArrList.removeAll()
        
    }
    
    override func viewDidAppear(_ animated: Bool) {
        
        if(isDataSet == false){
            
            cntView.frame = self.contentView.frame
            
            cntView.isHidden = false
            
            
            getData()
        }
        
    }

    func setData(){
        self.navigationItem.title = vTitle
        self.title = vTitle
    }
    
    

    func getData(){
        if(loaderView != nil){
            loaderView.removeFromSuperview()
        }
        
        loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
        loaderView.backgroundColor = UIColor.clear
        
        self.cntView.isHidden = true
        
        let parameters = ["type":"getServiceTypes","iDriverId": GeneralFunctions.getMemberd(), "UserType":Utils.appUserType, "iVehicleCategoryId": iVehicleCategoryId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.dataArrList.removeAll()
                    self.chkStatusArr.removeAll()
                    
                    let msgDataArr = dataDict.getArrObj(Utils.message_str)
                    
                    for i in 0..<msgDataArr.count{
                        let item = msgDataArr[i] as! NSDictionary
                        
                        self.dataArrList += [item]
                        
                        if(item.get("VehicleServiceStatus") == "true"){
                            self.chkStatusArr += [true]
                        }else{
                            self.chkStatusArr += [false]
                        }
                    }
                    
                    self.tableView.reloadData()
                    
                    self.cntView.isHidden = false
                    
                }else{
//                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                    
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                        //                        self.loadData()
                        self.closeCurrentScreen()
                    })
                }
                
            }else{
//                self.generalFunc.setError(uv: self)
                self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: InternetConnection.isConnectedToNetwork() ? "Please try again later" : "No Internet Connection", key: InternetConnection.isConnectedToNetwork() ? "LBL_TRY_AGAIN_TXT" : "LBL_NO_INTERNET_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                    //                        self.loadData()
                    self.closeCurrentScreen()
                })
            }
            
            self.loaderView.isHidden = true
        })
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        let item = self.dataArrList[indexPath.item]
        if(item.get("ePriceType") == "Provider" && (item.get("eFareType") == "Fixed" || item.get("eFareType") == "Hourly")){
            return 110
        }else{
            return 55
        }
        
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
        let cell = tableView.dequeueReusableCell(withIdentifier: "UpdateServicesTVCell", for: indexPath) as! UpdateServicesTVCell
        
        let item = self.dataArrList[indexPath.item]
        cell.serviceLbl.text = item.get("vTitle")
        cell.serviceAmountLbl.text = self.generalFunc.getLanguageLabel(origValue: "Service Amount", key: "LBL_SERVICE_AMOUNT") + ": "
        cell.subTitleLbl.text = item.get("SubTitle")
        cell.editBtn.tag = indexPath.item
        cell.editBtn.btnType = "EDIT_SERVICE_AMOUNT"
        cell.editBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT"))
        cell.editBtn.clickDelegate = self
        
        if(item.get("eFareType") == "Fixed"){
            cell.priceLbl.text = item.get("vCurrencySymbol") + "" + Configurations.convertNumToAppLocal(numStr: item.get("fAmount"))
        }else{
//            cell.priceLbl.text = item.get("vCurrencySymbol") + "" + Configurations.convertNumToAppLocal(numStr: item.get("fPricePerHour")) + "/" + self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HOUR")
            
            cell.priceLbl.text = item.get("vCurrencySymbol") + "" + Configurations.convertNumToAppLocal(numStr: item.get("fAmount")) + "/" + self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HOUR")
        }
        if(item.get("ePriceType") == "Provider" && (item.get("eFareType") == "Fixed" || item.get("eFareType") == "Hourly")){
            cell.serviceAmountAreaView.isHidden = false
        }else{
            cell.serviceAmountAreaView.isHidden = true
        }
        cell.serviceChkBox.tag = indexPath.item
        GeneralFunctions.setCheckBoxTheme(chkBox: cell.serviceChkBox)
        
        let serviceSelectTapGue = UITapGestureRecognizer()
        serviceSelectTapGue.addTarget(self, action: #selector(self.selectServiceViewTapped(sender:)))

        cell.serviceCHKContainerView.isUserInteractionEnabled = true
        cell.serviceCHKContainerView.tag = indexPath.item
        cell.serviceCHKContainerView.addGestureRecognizer(serviceSelectTapGue)
        
//        serviceCHKContainerView
        
        if(self.chkStatusArr[indexPath.item] == true){
            cell.serviceChkBox.setOn(true, animated: true)
        }else{
            cell.serviceChkBox.setOn(false, animated: true)
        }
        cell.serviceChkBox.delegate = self
        cell.selectionStyle = .none
        return cell
    }
    
    func selectServiceViewTapped(sender:UITapGestureRecognizer){
        let index = sender.view!.tag
        
        self.chkStatusArr[index] = self.chkStatusArr[index] == true ? false : true
        tableView.reloadRows(at: [IndexPath(item: index, section: 0)], with: .none)
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
//        self.chkStatusArr[indexPath.item] = self.chkStatusArr[indexPath.item] == true ? false : true
//        tableView.reloadRows(at: [IndexPath(item: indexPath.item, section: indexPath.section)], with: .none)
    }
    
    func didTap(_ checkBox: BEMCheckBox) {
        self.chkStatusArr[checkBox.tag] = checkBox.on
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender.btnType == "EDIT_SERVICE_AMOUNT"){
            let item = self.dataArrList[sender.tag]
            var amount = ""
            
            if(item.get("eFareType") == "Fixed"){
                amount = Configurations.convertNumToAppLocal(numStr: item.get("fAmount"))
            }else{
                amount = Configurations.convertNumToAppLocal(numStr: item.get("fPricePerHour"))
            }
            
            let openEditServiceAmountView = OpenEditServiceAmountView(uv: self, containerView: self.contentView)
            openEditServiceAmountView.setHandler(handler: { (isNegativeBtnClicked, isPositiveBtnClicked, view, bgView) in
                if(isPositiveBtnClicked == true){
                    self.updateProviderAmount(amount_str: openEditServiceAmountView.amount_str, index: sender.tag, iVehicleTypeId: item.get("iVehicleTypeId"))
                }
            })
            openEditServiceAmountView.show(msg: self.generalFunc.getLanguageLabel(origValue: "Enter Service Amount Below:", key: "LBL_ENTER_SERVICE_AMOUNT"), currentAmount: amount, currencySymbol: item.get("vCurrencySymbol"))
        }else if(sender == self.updateServicesBtn){
            var carTypes_str = ""
            for i in 0..<self.chkStatusArr.count{
                if(self.chkStatusArr[i] == true){
                    let carTypeId = self.dataArrList[i].get("iVehicleTypeId")
                    carTypes_str = carTypes_str == "" ? carTypeId : (carTypes_str + "," + carTypeId)
                }
            }
            updateServices(carTypes: carTypes_str)
        }
    }

    func updateProviderAmount(amount_str:String, index:Int, iVehicleTypeId:String){
        
        let parameters = ["type":"UpdateDriverServiceAmount","iDriverId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "iVehicleTypeId": iVehicleTypeId, "fAmount": amount_str]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                        
                        self.getData()
                        
                    })
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
        })
    }
    
    func updateServices(carTypes:String){
        
            let parameters = ["type":"UpdateDriverVehicle","iDriverId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "vCarType": carTypes, "iVehicleCategoryId": iVehicleCategoryId]
            
            let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
            exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
            exeWebServerUrl.currInstance = exeWebServerUrl
            exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
                
                if(response != ""){
                    let dataDict = response.getJsonDataDict()
                    
                    if(dataDict.get("Action") == "1"){
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                            
                            self.closeCurrentScreen()
                            
                        })
                        
                    }else{
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self)
                }
                
            })
    }
}

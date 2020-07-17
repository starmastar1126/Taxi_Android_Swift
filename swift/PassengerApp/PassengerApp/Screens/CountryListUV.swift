//
//  CountryListUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 09/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class CountryListUV: UIViewController, UITableViewDelegate, UITableViewDataSource {

    @IBOutlet weak var tableView: UITableView!
    @IBOutlet weak var contentView: UIView!
    
    var fromRegister:Bool = false
    var fromVerifyProfile:Bool = false
    var fromEditProfile = false
    var fromAccountInfo = false
    
     var loader_IV:UIActivityIndicatorView?
    var selectedCountryHolder:countryHolder?
    
    var countryHolder_arr = [countryHolder()]
    
    var myCountryDict: [Int: [countryHolder]] = [Int: [countryHolder]]()
    
    let generalFunc = GeneralFunctions()
    
    var cntView:UIView!
    var isSafeAreaSet = false
    var sectionTitleIndexes = NSMutableArray()
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        cntView = self.generalFunc.loadView(nibName: "CountryListScreenDesign", uv: self, contentView: contentView)
        self.contentView.addSubview(cntView)

        self.tableView.tableFooterView = UIView()
        self.tableView.allowsSelection = true
        self.tableView.register(CountryListTVCell.self, forCellReuseIdentifier: "CountryListTVCell")
        self.tableView.register(UINib(nibName: "CountryListTVCell", bundle: nil), forCellReuseIdentifier: "CountryListTVCell")

        self.addBackBarBtn()
        
        self.tableView.contentInset = UIEdgeInsetsMake(0, 0, GeneralFunctions.getSafeAreaInsets().bottom, 0)
        
        loader_IV = self.addActivityIndicator()
        
        setData()
        
        loadCountry()
    }
    
    
    override func viewDidLayoutSubviews() {
        if(isSafeAreaSet == false){
            
            cntView.frame.size.height = cntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            isSafeAreaSet = true
        }
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_CONTRY")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_CONTRY")
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    
    func loadCountry(){
//        countryList
        let parameters = ["type":"countryList"]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
//            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    let countryListArr = dataDict.getArrObj("CountryList")
                    
                    for i in 0  ..< countryListArr.count {
                        let dict = countryListArr[i] as! NSDictionary
                        _ = dict.get("key")
                        _ = dict.get("TotalCount")
                        
                        let subListArr = dict.getArrObj("List")
                        
                        for j in 0  ..< subListArr.count {
                           let subDict = subListArr[j] as! NSDictionary
                            let vCountry = subDict.get("vCountry")
                            let vPhoneCode = subDict.get("vPhoneCode")
                            _ = subDict.get("iCountryId")
                            let vCountryCode = subDict.get("vCountryCode")
                            
                            let countryHolderObj:countryHolder = countryHolder()
                            countryHolderObj.vPhoneCode = vPhoneCode
                            countryHolderObj.countryName = vCountry
                            countryHolderObj.vCountryCode = vCountryCode
                            
                            self.countryHolder_arr += [countryHolderObj]
                            
                        }
                    
                    }
                    
                    self.countryHolder_arr.sort { $0.countryName.lowercased()  < $1.countryName.lowercased() }
                    
                    var i = 0, j = 0;
                    for val in UnicodeScalar("A").value...UnicodeScalar("Z").value
                    {
                        let x = String(describing: UnicodeScalar(val));
                        
                        var cList = [countryHolder]() as Array
                        
                        for i in 0 ..< self.countryHolder_arr.count {
                            
                            let countryTitle:NSString = self.countryHolder_arr[i].countryName as NSString
                            let first = String(describing: UnicodeScalar(countryTitle.character(at: 0)))
                            
                            if(x.lowercased() == first.lowercased()){
                                cList.append(self.countryHolder_arr[i])
                            }
                        }
                        
                        
                        if cList.count != 0
                        {
                            self.myCountryDict[i] = cList
                            i += 1
                            self.sectionTitleIndexes.add(j)
                        }
                        j += 1
                    }
                    
                    //            print("total in dict::" + String(self.myCountryDict[0]![0].countryName))
                    self.tableView.allowsSelection = true
                    self.tableView.dataSource = self
                    self.tableView.delegate = self
                    self.tableView.reloadData()
                    self.loader_IV?.removeFromSuperview()
                   
                }else{
//                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        self.closeCurrentScreen()
                    })
                }
                
            }else{
//                self.generalFunc.setError(uv: self)
                self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRY_AGAIN_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    self.closeCurrentScreen()
                })
            }
        })
    }

    func numberOfSections(in tableView: UITableView) -> Int {
        // #warning Incomplete implementation, return the number of sections
        return myCountryDict.count
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        
        return myCountryDict[section]!.count
    }
    
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "CountryListTVCell", for: indexPath) as! CountryListTVCell
        
        let country_holderObj = myCountryDict[indexPath.section]![indexPath.row]
        
        cell.countryLabelTxt.text = country_holderObj.countryName
        cell.countryLabelTxt.removeGestureRecognizer(cell.countryLabelTxt.tapGue)
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        self.selectedCountryHolder = myCountryDict[indexPath.section]![indexPath.row]
        
        if(fromRegister == true){
            performSegue(withIdentifier: "unwindToSignUp", sender: self)
        }else if(fromVerifyProfile == true){
            performSegue(withIdentifier: "setCountryToVFbUnWind", sender: self)
        }else if(fromEditProfile == true){
            performSegue(withIdentifier: "unwindToEditProfile", sender: self)
        }else if(fromAccountInfo == true){
            performSegue(withIdentifier: "unwindToAccountInfo", sender: self)
        }
        
    }
    
    func tableView(_ tableView: UITableView, titleForHeaderInSection section: Int) -> String? {
        //return String(describing: UnicodeScalar(65 + section)!)
        var indexTitle = ""
        var i = 0;
        for val in UnicodeScalar("A").value...UnicodeScalar("Z").value
        {
            if self.sectionTitleIndexes[section] as! Int == i
            {
                indexTitle = String(describing: UnicodeScalar(val)!)
            }
            i += 1
        }
        
        return indexTitle
    }
    
    func tableView(_ tableView: UITableView, sectionForSectionIndexTitle title: String,
                            at index: Int) -> Int{
        
        return index
    }
    
    /* section index titles displayed to the right of the `UITableView` */
     func sectionIndexTitles(for tableView: UITableView) -> [String]? {
        
        var indexTitle = [String()]
        indexTitle.removeAll()
        var i = 0;
        for val in UnicodeScalar("A").value...UnicodeScalar("Z").value
        {
            if self.sectionTitleIndexes.contains(i)
            {
                let x = String(describing: UnicodeScalar(val)!)
                indexTitle.append(x)
            }
            
            i += 1
        }
        
        return indexTitle
    }
}

class countryHolder  {
    var countryName: String = String()
    var vPhoneCode: String = String()
    var vCountryCode: String = String()
}

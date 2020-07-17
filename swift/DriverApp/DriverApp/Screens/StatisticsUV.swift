//
//  StatisticsUV.swift
//  DriverApp
//
//  Created by NEW MAC on 03/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class StatisticsUV: UIViewController, MyTxtFieldClickDelegate , ChartViewDelegate , IAxisValueFormatter {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var yearTxtField: MyTextField!
    @IBOutlet weak var numOfTripHLbl: MyLabel!
    @IBOutlet weak var numOfTripVLbl: MyLabel!
    @IBOutlet weak var totalEarningHLbl: MyLabel!
    @IBOutlet weak var totalEarningVLbl: MyLabel!
    @IBOutlet weak var graphView: UIView!
    
    let generalFunc = GeneralFunctions()
    
    let scrollView = UIScrollView()
    
    var isPageLoaded = false
    
    var cntView:UIView!
    
    var loaderView:UIView!
    
    var window:UIWindow!
    
    var yearListArr = [String]()
    
    let numberOfDataItems = 12
    
    var scGraphView:LineChartView!
    
    var currentSelectedYear = ""
    
    var totalEarningsData = [Double]()
    
    var monthsData = [String]()
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
    }
    
    override func viewWillDisappear(_ animated: Bool) {
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        window = Application.window!
        
        self.view.translatesAutoresizingMaskIntoConstraints = true
        
        self.contentView.addSubview(scrollView)
        
        cntView = self.generalFunc.loadView(nibName: "StatisticsScreenDesign", uv: self, contentView: contentView)
        
        scrollView.addSubview(cntView)
        
//        self.contentView.addSubview(self.generalFunc.loadView(nibName: "StatisticsScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
    }

    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoaded == false){
            scrollView.frame = self.contentView.frame
            
            
            let screenHeight = Application.screenSize.height - self.navigationController!.navigationBar.frame.height - UIApplication.shared.statusBarFrame.height
            
            cntView.frame.size = CGSize(width: contentView.frame.width, height: (self.contentView.frame.height > screenHeight ? self.contentView.frame.height : screenHeight))
            
            let yPosition = ((cntView.frame.height / 2) - (self.contentView.frame.height / 2)) >= 0 ? ((cntView.frame.height / 2) - (self.contentView.frame.height / 2)) : contentView.bounds.midY
            
            cntView.center = CGPoint(x: contentView.bounds.midX, y: contentView.bounds.midY + yPosition)
            
            scrollView.setContentViewSize()
            scrollView.bounces = false
            scrollView.backgroundColor = UIColor.clear
            
            DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(0.5 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                self.yearTxtField.addArrowView(color: UIColor(hex: 0xbcbcbc), transform: CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180)))
            })
            
            scGraphView = createDarkGraph(CGRect(x:0,y:0, width:contentView.frame.width, height: self.graphView.frame.height))
            
            
            self.graphView.addSubview(scGraphView)
            setData()
            
            getData(currentSelectedYear: currentSelectedYear)
            
            yearTxtField.disableMenu()
            isPageLoaded = true
        }
    }
    
    func stringForValue(_ value: Double, axis: AxisBase?) -> String {
        return monthsData[Int(value) % monthsData.count]
    }
    
    fileprivate func createDarkGraph(_ frame: CGRect) -> LineChartView {
        let graphView = LineChartView(frame: frame)
        
        graphView.delegate = self
        graphView.dragEnabled = true
        graphView.setScaleEnabled(true)
        graphView.pinchZoomEnabled = true
        
        graphView.viewPortHandler.setMaximumScaleX(CGFloat(10))
        graphView.viewPortHandler.setMaximumScaleY(CGFloat(5))
        
        let xAxis = graphView.xAxis
        xAxis.labelPosition = .bottom
        xAxis.valueFormatter = self
        xAxis.granularity = 1
        xAxis.drawAxisLineEnabled = true
        xAxis.drawGridLinesEnabled = false
        xAxis.axisMinimum = 0.0
        
        let leftAxis = graphView.leftAxis
        leftAxis.axisMinimum = 0
        leftAxis.drawAxisLineEnabled = false
        leftAxis.drawGridLinesEnabled = false
        leftAxis.drawLabelsEnabled = false
        leftAxis.drawZeroLineEnabled = true
        
        
        let rightAxis = graphView.rightAxis
        rightAxis.axisMinimum = 0
        rightAxis.drawAxisLineEnabled = false
        rightAxis.drawGridLinesEnabled = false
        rightAxis.drawLabelsEnabled = false
        rightAxis.drawZeroLineEnabled = true
        
//        let marker = BalloonMarker(color: UIColor(white: 180/255, alpha: 1),
//                                   font: .systemFont(ofSize: 12),
//                                   textColor: .white,
//                                   insets: UIEdgeInsets(top: 8, left: 8, bottom: 20, right: 8))
//        marker.graphView = graphView
//        marker.minimumSize = CGSize(width: 80, height: 40)
//        graphView.marker = marker
        
        graphView.legend.form = .default
//        graphView.animate(xAxisDuration: 2.5)
    
        return graphView
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRIP_STATISTICS_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRIP_STATISTICS_TXT")
        
        self.yearTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YEAR"))
        self.totalEarningHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Total Earnings", key: "LBL_TOTAL_EARNINGS")
        self.numOfTripHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Number of trips", key: "LBL_NUMBER_OF_TRIPS")
        
        
        self.yearTxtField.getTextField()!.clearButtonMode = .never
        self.yearTxtField.setEnable(isEnabled: false)
        self.yearTxtField.myTxtFieldDelegate = self
    }
    
    func myTxtFieldTapped(sender: MyTextField) {
        if(sender == self.yearTxtField){
            let openListView = OpenListView(uv: self, containerView: self.view)
            openListView.show(listObjects: self.yearListArr, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_YEAR"), currentInst: openListView, handler: { (selectedItemId) in
                self.yearTxtField.setText(text: self.yearListArr[selectedItemId])
                self.yearChanged()
            })
        }
    }
    func getData(currentSelectedYear:String){
        scrollView.isHidden = true
        loaderView =  self.generalFunc.addMDloader(contentView: self.view)
        loaderView.backgroundColor = UIColor.clear
        
        self.yearListArr.removeAll()
        self.monthsData = []
        self.totalEarningsData = []
        
        let parameters = ["type":"getYearTotalEarnings","iDriverId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "year": currentSelectedYear]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.loaderView.isHidden = true
                    self.scrollView.isHidden = false
                    
                    let yearArr = dataDict.getObj(Utils.message_str).getArrObj("YearArr")
                    
                    for i in 0..<yearArr.count{
                        self.yearListArr += [yearArr[i] as! String]
                        
                        if(i == 0 && Utils.getText(textField: self.yearTxtField.getTextField()!) == ""){
                            self.yearTxtField.setText(text: "\(self.yearListArr[0])")
                        }
                    }
                    
                    let yearMonthArr = dataDict.getObj(Utils.message_str).getArrObj("YearMonthArr")
                    
                    for i in 0..<yearMonthArr.count{
                        let item = yearMonthArr[i] as! NSDictionary
                        
                        self.monthsData += [item.get("CurrentMonth")]
                        self.totalEarningsData += [GeneralFunctions.parseDouble(origValue: 0.0, data: item.get("TotalEarnings"))]
                    }
                    
//                    if(self.yearPicker == nil){
//
//                    DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(0.5 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//
//                            self.yearPicker = DownPicker(textField: self.yearTxtField.getTextField()!, withData: self.yearListArr as [AnyObject])
//                            self.yearPicker.setPlaceholder(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YEAR"))
//                            self.yearPicker.addTarget(self, action: #selector(self.yearChanged), for: .valueChanged)
//
//                            self.yearPicker.selectedIndex = 0
//
//                    })
//                    }
                    if(self.scGraphView != nil){
                        self.scGraphView.removeFromSuperview()
                    }
                    
                    self.scGraphView = self.createDarkGraph(CGRect(x:10,y:0, width:self.contentView.frame.width - 20, height: self.graphView.frame.height))
                    
//                    self.scGraphView.chartYMax = GeneralFunctions.parseDouble(origValue: 0.0, data: dataDict.get("MaxEarning"))
                    
                    self.totalEarningVLbl.text = dataDict.get("TotalEarning")
                    self.numOfTripVLbl.text = dataDict.get("TripCount")
                    self.setLineGraphData()
                    self.graphView.addSubview(self.scGraphView)
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func setLineGraphData(){
        let entries = (0..<monthsData.count).map { (i) -> ChartDataEntry in
            let earningAmount : Double = totalEarningsData[i]
            return ChartDataEntry(x: Double(i), y: earningAmount)
        }
        
        let lineChartDataSet = LineChartDataSet(values: entries, label: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EARNING"))
        lineChartDataSet.drawIconsEnabled = false
//        lineChartDataSet.lineDashLengths = [1, 1]
        lineChartDataSet.highlightLineDashLengths = [1, 1]
        lineChartDataSet.setColor(UIColor.UCAColor.AppThemeColor)
        lineChartDataSet.setCircleColor(UIColor.UCAColor.AppThemeColor)
        lineChartDataSet.lineWidth = 1.5
        lineChartDataSet.circleRadius = 5
        lineChartDataSet.drawCircleHoleEnabled = false
        lineChartDataSet.valueFont = .systemFont(ofSize: 14)
        lineChartDataSet.formLineDashLengths = [1, 1]
        lineChartDataSet.formLineWidth = 1
        lineChartDataSet.formLineWidth = 15
        lineChartDataSet.mode = .linear
        lineChartDataSet.drawVerticalHighlightIndicatorEnabled = false
        lineChartDataSet.drawHorizontalHighlightIndicatorEnabled = false

        let gradientColors = [UIColor.UCAColor.AppThemeColor_Dark.lighter(by: 90)!.cgColor,
                              UIColor.UCAColor.AppThemeColor_Dark.lighter(by: 30)!.cgColor]
        let gradient = CGGradient(colorsSpace: nil, colors: gradientColors as CFArray, locations: nil)!
        
        lineChartDataSet.fillAlpha = 1
        lineChartDataSet.fill = Fill(linearGradient: gradient, angle: 90)
        lineChartDataSet.drawFilledEnabled = true
        
        let data = LineChartData(dataSet: lineChartDataSet)
        
        scGraphView.data = data
    }
    
    func yearChanged(){
        self.getData(currentSelectedYear: Utils.getText(textField: self.yearTxtField.getTextField()!))
    }

    // Data Generation
    private func generateRandomData(_ numberOfItems: Int, max: Double) -> [Double] {
        var data = [Double]()
        for _ in 0 ..< numberOfItems {
            var randomNumber = Double(arc4random()).truncatingRemainder(dividingBy: max)
            
            if(arc4random() % 100 < 10) {
                randomNumber *= 3
            }
            
            data.append(randomNumber)
        }
        return data
    }
    
    private func generateSequentialLabels(_ numberOfItems: Int, text: String) -> [String] {
        var labels = [String]()
        for i in 0 ..< numberOfItems {
            labels.append("\(text) \(i+1)")
        }
        return labels
    }
}

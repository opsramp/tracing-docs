using Microsoft.AspNetCore.Mvc;
using System.Diagnostics;

namespace YourNamespace
{
    public class HomeController : Controller
    {
        public IActionResult Index()
        {
            // Track work inside of the request
            using var activity = DiagnosticsConfig.ActivitySource.StartActivity("SayHello");
            activity?.SetTag("foo", 1);
            activity?.SetTag("bar", "Hello, World!");
            activity?.SetTag("baz", new int[] { 1, 2, 3 });

            return View();
        }
    }
}

